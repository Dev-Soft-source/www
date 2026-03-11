<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\NotificationMessage;
use App\Models\NotificationMessageDetail;
use Illuminate\Http\Request;

class NotificationMessageSettingController extends Controller
{
    public function index()
    {
        $languages = Language::orderByRaw('is_default DESC')->orderBy('id')->get(['id', 'name', 'abbreviation', 'is_default']);
        $messages = NotificationMessage::with(['details' => fn ($q) => $q->whereIn('language_id', $languages->pluck('id'))])
            ->orderBy('id')
            ->get();

        $rows = $messages->values()->map(function ($message, $index) use ($languages) {
            $langValues = [];
            foreach ($message->details as $detail) {
                $langValues[(int) $detail->language_id] = (string) ($detail->message ?? '');
            }

            foreach ($languages as $language) {
                $langValues[$language->id] = $langValues[$language->id] ?? '';
            }

            return [
                'id' => $message->id,
                'no' => $index + 1,
                'slug' => $message->slug,
                'name' => $message->name,
                'placeholders' => $message->placeholders ?? [],
                'languages' => $langValues,
            ];
        });

        return response()->json([
            'status' => 'Success',
            'message' => 'Data retrieved successfully.',
            'data' => [
                'languages' => $languages,
                'rows' => $rows,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'slug' => 'required|string|max:191|unique:notification_messages,slug',
            'name' => 'nullable|string|max:191',
            'placeholders' => 'nullable|array',
            'languages' => 'nullable|array',
        ]);

        $message = NotificationMessage::create([
            'slug' => trim($validated['slug']),
            'name' => trim((string) ($validated['name'] ?? '')),
            'placeholders' => array_values(array_filter($validated['placeholders'] ?? [])),
        ]);

        foreach (Language::select('id')->get() as $language) {
            NotificationMessageDetail::create([
                'notification_message_id' => $message->id,
                'language_id' => $language->id,
                'message' => trim((string) ($request->input("languages.{$language->id}") ?? '')),
            ]);
        }

        return response()->json([
            'status' => 'Success',
            'message' => 'Notification message created successfully.',
            'data' => ['id' => $message->id],
        ]);
    }

    public function update(Request $request, $id)
    {
        $message = NotificationMessage::findOrFail($id);

        $validated = $request->validate([
            'slug' => 'required|string|max:191|unique:notification_messages,slug,' . $message->id,
            'name' => 'nullable|string|max:191',
            'placeholders' => 'nullable|array',
            'languages' => 'nullable|array',
        ]);

        $message->update([
            'slug' => trim($validated['slug']),
            'name' => trim((string) ($validated['name'] ?? '')),
            'placeholders' => array_values(array_filter($validated['placeholders'] ?? [])),
        ]);

        foreach (($validated['languages'] ?? []) as $languageId => $value) {
            NotificationMessageDetail::updateOrCreate(
                [
                    'notification_message_id' => $message->id,
                    'language_id' => (int) $languageId,
                ],
                [
                    'message' => trim((string) $value),
                ]
            );
        }

        return response()->json([
            'status' => 'Success',
            'message' => 'Notification message updated successfully.',
        ]);
    }

    public function destroy($id)
    {
        $message = NotificationMessage::findOrFail($id);
        NotificationMessageDetail::where('notification_message_id', $message->id)->delete();
        $message->delete();

        return response()->json([
            'status' => 'Success',
            'message' => 'Notification message deleted successfully.',
        ]);
    }
}
