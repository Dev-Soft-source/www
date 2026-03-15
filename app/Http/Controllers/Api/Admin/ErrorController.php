<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ErrorController extends Controller
{
    use StatusResponser;

    public function index()
    {
        try {
            $languages = getAllLanguages();
            foreach ($languages as $language) {
                if (empty($language->abbreviation)) {
                    $language->validation = [];
                    continue;
                }
                
                $validationFilePath = lang_path($language->abbreviation) . '/validation.php';
                if (file_exists($validationFilePath)) {
                    try {
                        $validation = File::getRequire($validationFilePath);
                        if (is_array($validation)) {
                            // Store custom errors separately
                            $customErrors = isset($validation['custom']) ? $validation['custom'] : [];
                            $language->custom_validation = $customErrors;
                            
                            // Remove custom and attributes from main validation
                            unset($validation['custom']);
                            unset($validation['attributes']);
                            $language->validation = $validation;
                        } else {
                            $language->validation = [];
                            $language->custom_validation = [];
                        }
                    } catch (\Exception $e) {
                        \Log::error('Error reading validation file: ' . $validationFilePath . ' - ' . $e->getMessage());
                        $language->validation = [];
                        $language->custom_validation = [];
                    }
                } else {
                    $language->validation = [];
                    $language->custom_validation = [];
                }
            }

            return $this->successResponse($languages, 'Language has been added successfully.');
        } catch (\Exception $e) {
            \Log::error('Error in ErrorController@index: ' . $e->getMessage());
            return response()->json($this->errorResponse('An error occurred while fetching language errors.'), 500);
        }
    }

    public function update(Request $request)
    {
        $rules = [
            'language_id' => ['required', 'exists:App\Models\Language,id'],
            'value' => ['required', 'string'],
            'field' => ['required', 'string'],
        ];
        $this->validate($request, $rules);

        $language = Language::whereId($request->language_id)->first();

        $validationFilePath = lang_path($language->abbreviation) . '/validation.php';
        if (file_exists($validationFilePath)) {
            $validation = File::getRequire($validationFilePath);
            
            // Handle custom errors (format: custom.field.rule or custom.field or custom.field.rule.subrule)
            if (strpos($request->field, 'custom.') === 0) {
                $fieldPath = substr($request->field, 7); // Remove 'custom.' prefix
                
                if (!isset($validation['custom'])) {
                    $validation['custom'] = [];
                }
                
                // Check if this is a nested structure (field.rule) or flat key with dots (like dr_amount.gt)
                // First, try to find if the field exists as a nested structure
                $parts = explode('.', $fieldPath);
                
                if (count($parts) >= 2) {
                    // Check if first part exists as an array key in custom
                    $firstPart = $parts[0];
                    if (isset($validation['custom'][$firstPart]) && is_array($validation['custom'][$firstPart])) {
                        // It's nested: custom.field.rule
                        if (count($parts) === 2) {
                            $field = $parts[0];
                            $rule = $parts[1];
                            if (!isset($validation['custom'][$field])) {
                                $validation['custom'][$field] = [];
                            }
                            $validation['custom'][$field][$rule] = $request->value;
                        } else {
                            // Deeper nesting (shouldn't happen in current structure, but handle it)
                            $current = &$validation['custom'];
                            for ($i = 0; $i < count($parts) - 1; $i++) {
                                if (!isset($current[$parts[$i]])) {
                                    $current[$parts[$i]] = [];
                                }
                                $current = &$current[$parts[$i]];
                            }
                            $current[$parts[count($parts) - 1]] = $request->value;
                        }
                    } else {
                        // It's a flat key with dots (like dr_amount.gt)
                        $validation['custom'][$fieldPath] = $request->value;
                    }
                } else {
                    // Single level custom field
                    $validation['custom'][$fieldPath] = $request->value;
                }
            } else {
                // Handle main validation errors
                // Check if it's a nested main error (like between.array)
                if (strpos($request->field, '.') !== false) {
                    $parts = explode('.', $request->field);
                    if (count($parts) === 2 && isset($validation[$parts[0]]) && is_array($validation[$parts[0]])) {
                        // Nested main error
                        $validation[$parts[0]][$parts[1]] = $request->value;
                    } else {
                        // Flat key with dots in main errors
                        $validation[$request->field] = $request->value;
                    }
                } else {
                    $validation[$request->field] = $request->value;
                }
            }

            $content = "<?php\n\nreturn\n[\n";
            foreach ($validation as $key => $value) {
                if (is_array($value)) {
                    $content .= "\t'" . $key . "' => \n[\n";
                    foreach ($value as $k => $v) {
                        if (is_array($v)) {
                            $content .= "\t\t'" . $k . "' => \n\t\t[\n";
                            foreach ($v as $k1 => $v1) {
                                if (is_array($v1)) {
                                    $content .= "\t\t\t'" . $k1 . "' => \n\t\t\t[\n";
                                    foreach ($v1 as $k2 => $v2) {
                                        $content .= "\t\t\t\t'" . $k2 . "' => '" . addslashes($v2) . "',\n";
                                    }
                                    $content .= "\t\t\t],\n";
                                } else {
                                    $content .= "\t\t\t'" . $k1 . "' => '" . addslashes($v1) . "',\n";
                                }
                            }
                            $content .= "\t\t],\n";
                        } else {
                            $content .= "\t\t'" . $k . "' => '" . addslashes($v) . "',\n";
                        }
                    }
                    $content .= "\t],\n";
                } else {
                    $content .= "\t'" . $key . "' => '" . addslashes($value) . "',\n";
                }
            }

            $content .= "];";
            file_put_contents($validationFilePath, $content);
            return $this->successResponse([], 'Error has been updated successfully.');
        }
        
        return $this->errorResponse('Validation file not found.');
    }
}
