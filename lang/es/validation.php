<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'El campo :attribute debe ser aceptado.',
    'accepted_if' => 'El campo :attribute debe ser aceptado cuando :other es :value.',
    'active_url' => 'El campo :attribute debe ser una URL válida.',
    'after' => 'El campo :attribute debe ser una fecha posterior a :date.',
    'after_or_equal' => 'El campo :attribute debe ser una fecha posterior o igual a :date.',
    'alpha' => 'El campo :attribute solo debe contener letras.',
    'alpha_dash' => 'El campo :attribute solo debe contener letras, números, guiones y guiones bajos.',
    'alpha_num' => 'El campo :attribute solo debe contener letras y números.',
    'array' => 'El campo :attribute debe ser un array.',
    'ascii' => 'El campo :attribute solo debe contener caracteres alfanuméricos y símbolos de un solo byte.',
    'before' => 'El campo :attribute debe ser una fecha anterior a :date.',
    'before_or_equal' => 'El campo :attribute debe ser una fecha anterior o igual a :date.',
    'between' => [
        'array' => 'El campo :attribute debe tener entre :min y :max elementos.',
        'file' => 'El campo :attribute debe tener entre :min y :max kilobytes.',
        'numeric' => 'El campo :attribute debe estar entre :min y :max.',
        'string' => 'El campo :attribute debe tener entre :min y :max caracteres.',
    ],
    'boolean' => 'El campo :attribute debe ser verdadero o falso.',
    'can' => 'El campo :attribute contiene un valor no autorizado.',
    'confirmed' => 'La confirmación del campo :attribute no coincide.',
    'current_password' => 'La contraseña es incorrecta.',
    'date' => 'El campo :attribute debe ser una fecha válida.',
    'date_equals' => 'El campo :attribute debe ser una fecha igual a :date.',
    'date_format' => 'El campo :attribute debe coincidir con el formato :format.',
    'decimal' => 'El campo :attribute debe tener :decimal lugares decimales.',
    'declined' => 'El campo :attribute debe ser rechazado.',
    'declined_if' => 'El campo :attribute debe ser rechazado cuando :other es :value.',
    'different' => 'El campo :attribute y :other deben ser diferentes.',
    'digits' => 'El campo :attribute debe tener :digits dígitos.',
    'digits_between' => 'El campo :attribute debe tener entre :min y :max dígitos.',
    'dimensions' => 'El campo :attribute tiene dimensiones de imagen inválidas.',
    'distinct' => 'El campo :attribute tiene un valor duplicado.',
    'doesnt_end_with' => 'El campo :attribute no debe terminar con uno de los siguientes: :values.',
    'doesnt_start_with' => 'El campo :attribute no debe comenzar con uno de los siguientes: :values.',
    'email' => 'El campo :attribute debe ser una dirección de correo electrónico válida.',
    'ends_with' => 'El campo :attribute debe terminar con uno de los siguientes: :values.',
    'enum' => 'El :attribute seleccionado no es válido.',
    'exists' => 'El :attribute seleccionado no es válido.',
    'extensions' => 'El campo :attribute debe tener una de las siguientes extensiones: :values.',
    'file' => 'El campo :attribute debe ser un archivo.',
    'filled' => 'El campo :attribute debe tener un valor.',
    'gt' => [
        'array' => 'El campo :attribute debe tener más de :value elementos.',
        'file' => 'El campo :attribute debe ser mayor que :value kilobytes.',
        'numeric' => 'El campo :attribute debe ser mayor que :value.',
        'string' => 'El campo :attribute debe ser mayor que :value caracteres.',
    ],
    'gte' => [
        'array' => 'El campo :attribute debe tener :value elementos o más.',
        'file' => 'El campo :attribute debe ser mayor o igual que :value kilobytes.',
        'numeric' => 'El campo :attribute debe ser mayor o igual que :value.',
        'string' => 'El campo :attribute debe ser mayor o igual que :value caracteres.',
    ],
    'hex_color' => 'El campo :attribute debe ser un color hexadecimal válido.',
    'image' => 'El campo :attribute debe ser una imagen.',
    'in' => 'El :attribute seleccionado no es válido.',
    'in_array' => 'El campo :attribute debe existir en :other.',
    'integer' => 'El campo :attribute debe ser un entero.',
    'ip' => 'El campo :attribute debe ser una dirección IP válida.',
    'ipv4' => 'El campo :attribute debe ser una dirección IPv4 válida.',
    'ipv6' => 'El campo :attribute debe ser una dirección IPv6 válida.',
    'json' => 'El campo :attribute debe ser una cadena JSON válida.',
    'lowercase' => 'El campo :attribute debe estar en minúsculas.',
    'lt' => [
        'array' => 'El campo :attribute debe tener menos de :value elementos.',
        'file' => 'El campo :attribute debe ser menor que :value kilobytes.',
        'numeric' => 'El campo :attribute debe ser menor que :value.',
        'string' => 'El campo :attribute debe ser menor que :value caracteres.',
    ],
    'lte' => [
        'array' => 'El campo :attribute no debe tener más de :value elementos.',
        'file' => 'El campo :attribute debe ser menor o igual que :value kilobytes.',
        'numeric' => 'El campo :attribute debe ser menor o igual que :value.',
        'string' => 'El campo :attribute debe ser menor o igual que :value caracteres.',
    ],
    'mac_address' => 'El campo :attribute debe ser una dirección MAC válida.',
    'max' => [
        'array' => 'El campo :attribute no debe tener más de :max elementos.',
        'file' => 'El campo :attribute no debe ser mayor que :max kilobytes.',
        'numeric' => 'El campo :attribute no debe ser mayor que :max.',
        'string' => 'El campo :attribute no debe ser mayor que :max caracteres.',
    ],
    'max_digits' => 'El campo :attribute no debe tener más de :max dígitos.',
    'mimes' => 'El campo :attribute debe ser un archivo de tipo: :values.',
    'mimetypes' => 'El campo :attribute debe ser un archivo de tipo: :values.',
    'min' => [
        'array' => 'El campo :attribute debe tener al menos :min elementos.',
        'file' => 'El campo :attribute debe tener al menos :min kilobytes.',
        'numeric' => 'El campo :attribute debe ser al menos :min.',
        'string' => 'El campo :attribute debe tener al menos :min caracteres.',
    ],
    'min_digits' => 'El campo :attribute debe tener al menos :min dígitos.',
    'missing' => 'El campo :attribute debe estar ausente.',
    'missing_if' => 'El campo :attribute debe estar ausente cuando :other es :value.',
    'missing_unless' => 'El campo :attribute debe estar ausente a menos que :other esté en :values.',
    'missing_with' => 'El campo :attribute debe estar ausente cuando :values está presente.',
    'missing_with_all' => 'El campo :attribute debe estar ausente cuando :values están presentes.',
    'multiple_of' => 'El campo :attribute debe ser un múltiplo de :value.',
    'not_in' => 'El :attribute seleccionado no es válido.',
    'not_regex' => 'El formato del campo :attribute no es válido.',
    'numeric' => 'El campo :attribute debe ser un número.',
    'password' => 'La contraseña es incorrecta.',
    'password_letters' => 'El campo :attribute debe contener al menos una letra.',
    'password_mixed' => 'El campo :attribute debe contener al menos una letra mayúscula y una minúscula.',
    'password_numbers' => 'El campo :attribute debe contener al menos un número.',
    'password_symbols' => 'El campo :attribute debe contener al menos un símbolo.',
    'password_uncompromised' => 'El :attribute dado ha aparecido en una filtración de datos. Por favor, elige un :attribute diferente.',
    'present' => 'El campo :attribute debe estar presente.',
    'present_if' => 'El campo :attribute debe estar presente cuando :other es :value.',
    'present_unless' => 'El campo :attribute debe estar presente a menos que :other esté en :values.',
    'present_with' => 'El campo :attribute debe estar presente cuando :values está presente.',
    'present_with_all' => 'El campo :attribute debe estar presente cuando :values están presentes.',
    'prohibited' => 'El campo :attribute está prohibido.',
    'prohibited_if' => 'El campo :attribute está prohibido cuando :other es :value.',
    'prohibited_unless' => 'El campo :attribute está prohibido a menos que :other esté en :values.',
    'prohibits' => 'El campo :attribute prohíbe que :other esté presente.',
    'regex' => 'El formato del campo :attribute no es válido.',
    'required' => 'Este campo es obligatorio.',
    'required_array_keys' => 'El campo :attribute debe contener entradas para: :values.',
    'required_if' => 'El campo :attribute es obligatorio cuando :other es :value.',
    'required_if_accepted' => 'El campo :attribute es obligatorio cuando :other es aceptado.',
    'required_unless' => 'El campo :attribute es obligatorio a menos que :other esté en :values.',
    'required_with' => 'El campo :attribute es obligatorio cuando :values está presente.',
    'required_with_all' => 'El campo :attribute es obligatorio cuando :values están presentes.',
    'required_without' => 'El campo :attribute es obligatorio cuando :values no está presente.',
    'required_without_all' => 'El campo :attribute es obligatorio cuando ninguno de :values está presente.',
    'same' => 'El campo :attribute debe coincidir con :other.',
    'size' => [
        'array' => 'El campo :attribute debe contener :size elementos.',
        'file' => 'El campo :attribute debe ser :size kilobytes.',
        'numeric' => 'El campo :attribute debe ser :size.',
        'string' => 'El campo :attribute debe ser :size caracteres.',
    ],
    'starts_with' => 'El campo :attribute debe comenzar con uno de los siguientes: :values.',
    'string' => 'El campo :attribute debe ser una cadena.',
    'timezone' => 'El campo :attribute debe ser una zona horaria válida.',
    'unique' => 'El :attribute ya ha sido tomado.',
    'uploaded' => 'El :attribute falló al subir.',
    'uppercase' => 'El campo :attribute debe estar en mayúsculas.',
    'url' => 'El campo :attribute debe ser una URL válida.',
    'ulid' => 'El campo :attribute debe ser un ULID válido.',
    'uuid' => 'El campo :attribute debe ser un UUID válido.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "rule.attribute" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'make' => [
            'required' => 'La marca es obligatoria.',
        ],
        'model' => [
            'required' => 'El modelo es obligatorio.',
        ],
        'type' => [
            'required' => 'El tipo de vehículo es obligatorio.',
        ],
        'liscense_no' => [
            'required' => 'El número de placa es obligatorio.',
            'max' => 'El número de placa debe tener menos de 8 caracteres.',
        ],
        'color' => [
            'required' => 'El color es obligatorio.',
            'max' => 'El color debe tener menos de 15 caracteres.',
        ],
        'year' => [
            'required' => 'El año es obligatorio.',
            'max' => 'El año debe tener menos de 4 caracteres.',
        ],
        'car_type' => [
            'required' => 'La fuente de energía es obligatoria.',
        ],
        'primary_vehicle' => [
            'required' => 'El vehículo principal es obligatorio.',
        ],
        'image' => [
            'required_without' => 'La foto del vehículo es obligatoria.',
            'image' => 'La foto del vehículo debe ser una imagen.',
            'mimes' => 'La foto del vehículo debe ser un archivo de tipo: jpeg, png, jpg, gif.',
            'max' => 'La foto del vehículo debe ser inferior a 10MB.',
        ],
        'city_not_in_record' => [
            'message' => 'No hemos encontrado este nombre de ciudad en nuestros registros, por favor verifica la ortografía.',
        ],
        'from' => [
            'required' => 'El origen es obligatorio.',
        ],
        'to' => [
            'required' => 'El destino es obligatorio.',
        ],
        'pickup' => [
            'required' => 'El punto de recogida es obligatorio.',
        ],
        'dropoff' => [
            'required' => 'El punto de llegada es obligatorio.',
        ],
        'time' => [
            'required' => 'La hora es obligatoria.',
        ],
        'details' => [
            'required' => 'Los detalles son obligatorios.',
        ],
        'seats' => [
            'required' => 'Selecciona el número de asientos.',
        ],
        'smoke' => [
            'required' => 'Selecciona una opción de fumadores.',
        ],
        'animal_friendly' => [
            'required' => 'Selecciona una opción apta para animales.',
        ],
        'booking_method' => [
            'required' => 'Selecciona un método de reserva.',
        ],
        'luggage' => [
            'required' => 'Selecciona una opción de equipaje.',
        ],
        'price' => [
            'required' => 'El precio es obligatorio.',
        ],
        'payment_method' => [
            'required' => 'Selecciona un método de pago.',
        ],
        'middle_seats' => [
            'required' => 'Selecciona los asientos del medio.',
        ],
        'back_seats' => [
            'required' => 'Selecciona los asientos traseros.',
        ],
        'agree_terms' => [
            'accepted' => 'Debes aceptar los términos para continuar.',
        ],
        'email' => [
            'required' => 'La dirección de correo electrónico es obligatoria.',
            'email' => 'Introduce una dirección de correo electrónico válida, por ejemplo, name@example.com.',
            'unique' => 'Ya existe una cuenta con esta dirección de correo electrónico.',
        ],
        'package' => [
            'required' => 'El paquete es obligatorio.',
        ],
        'custom_amount' => [
            'required' => 'El importe personalizado es obligatorio.',
        ],
        'name' => [
            'required' => 'El nombre es obligatorio.',
        ],
        'payment_method' => [
            'required' => 'El método de pago es obligatorio.',
            'in' => 'El método de pago no es válido.',
        ],
        'donation_acknowledgment' => [
            'required' => 'El reconocimiento de la donación es obligatorio.',
        ],
        'terms_privacy' => [
            'required' => 'Los términos y la privacidad son obligatorios.',
        ],
        'name_on_card' => [
            'required_if' => 'El nombre del titular de la tarjeta es obligatorio cuando se paga con tarjeta.',
        ],
        'card_element' => [
            'required_if' => 'Los datos de la tarjeta son obligatorios cuando se paga con tarjeta.',
        ],
        'old_email' => [
            'required' => 'El correo electrónico actual es obligatorio.',
            'email' => 'El correo electrónico actual no es una dirección de correo electrónico válida.',
        ],
        'email_confirmation' => [
            'required' => 'La confirmación del correo electrónico es obligatoria.',
            'email' => 'La confirmación del correo electrónico no es una dirección de correo electrónico válida.',
        ],
        'first_name' => [
            'required' => 'El nombre es obligatorio.',
            'regex' => 'El nombre solo puede contener letras, espacios y guiones.',
        ],
        'last_name' => [
            'required' => 'El apellido es obligatorio.',
            'regex' => 'El apellido solo puede contener letras, espacios y guiones.',
        ],
        'password' => [
            'required' => 'La contraseña es obligatoria.',
            'min' => 'La contraseña debe tener al menos :min caracteres.',
            'regex' => 'La contraseña debe incluir una letra mayúscula, una minúscula, un número y un símbolo.',
        ],
        'password_confirmation' => [
            'required' => 'La confirmación de la contraseña es obligatoria.',
            'same' => 'Las contraseñas no coinciden.',
        ],
        'pass1' => [
            'required' => 'La contraseña actual es obligatoria.',
        ],
        'pass2' => [
            'required' => 'La nueva contraseña es obligatoria.',
            'string' => 'La nueva contraseña debe ser una cadena de texto.',
            'min' => 'La nueva contraseña debe tener al menos :min caracteres.',
            'regex' => 'La nueva contraseña debe contener al menos una letra mayúscula, una minúscula, un número y un carácter especial.',
        ],
        'pass3' => [
            'required' => 'La confirmación de la nueva contraseña es obligatoria.',
            'string' => 'La confirmación de la nueva contraseña debe ser una cadena de texto.',
            'same' => 'La confirmación de la contraseña no coincide con la nueva contraseña.',
        ],
        'agree_cost_share_terms' => [
            'required' => 'Confirma que aceptas la regla de reparto de costos.',
            'accepted' => 'Confirma que aceptas la regla de reparto de costos.',
        ],
        'rideshare_disclaimer' => [
            'required' => 'Este reconocimiento es obligatorio antes de continuar.',
            'accepted' => 'Este reconocimiento es obligatorio antes de continuar.',
        ],
        'gender' => [
            'required' => 'El genero es obligatorio.',
        ],
        'dob' => [
            'required' => 'La fecha de nacimiento es obligatoria.',
            'date' => 'La fecha de nacimiento debe ser una fecha valida.',
        ],
        'country' => [
            'required' => 'El pais es obligatorio.',
        ],
        'address' => [
            'string' => 'La direccion debe ser una cadena de texto.',
            'max' => 'La direccion no debe superar los :max caracteres.',
        ],
        'state' => [
            'required' => 'El estado es obligatorio.',
            'string' => 'El estado debe ser una cadena de texto.',
            'max' => 'El estado no debe superar los :max caracteres.',
        ],
        'city' => [
            'required' => 'La ciudad es obligatoria.',
            'string' => 'La ciudad debe ser una cadena de texto.',
            'max' => 'La ciudad no debe superar los :max caracteres.',
        ],
        'zipcode' => [
            'required' => 'El codigo postal es obligatorio.',
            'string' => 'El codigo postal debe ser una cadena de texto.',
            'max' => 'El codigo postal no debe superar los :max caracteres.',
        ],
        'government_issued_id' => [
            'file' => 'La identificacion oficial debe ser un archivo.',
            'mimes' => 'La identificacion oficial debe ser un archivo de tipo: :values.',
            'max' => 'La identificacion oficial debe ser inferior a 10 MB.',
        ],
        'bio' => [
            'required' => 'La biografia es obligatoria.',
            'string' => 'La biografia debe ser una cadena de texto.',
            'max' => 'La biografia no debe superar los :max caracteres.',
        ],
        'phone' => [
            'required' => 'El número de teléfono es obligatorio.',
            'valid' => 'Por favor, introduce un número de teléfono válido.',
            'unique' => 'Ya existe una cuenta con este número de teléfono.',
        ],
        'full_phone' => [
            'max' => 'El número de teléfono debe tener menos de 20 caracteres.',
            'unique' => 'Este número de teléfono ya ha sido registrado.',
        ],
        'message' => [
            'required' => 'El mensaje es obligatorio.',
        ],
        'student_card' => [
            'required' => 'La tarjeta de estudiante es obligatoria.',
            'mimes' => 'La imagen debe ser un archivo de tipo: jpeg, png, jpg, gif, pdf.',
            'max' => 'La imagen debe ser menor a 10MB.',
        ],

        'payment_method' => [
            'required' => 'El método de pago es obligatorio.',
        ],
        'card_id' => [
            'required' => 'Selecciona una opción de pago.',
        ],
        'dr_amount' => [
            'required' => 'El importe es obligatorio.',
        ],
        'dr_amount.gt' => 'El importe debe ser mayor que 0.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'make' => 'Marca',
        'model' => 'Modelo',
        'type' => 'Tipo de Vehículo',
        'color' => 'Color',
        'year' => 'Año',
        'liscense_no' => 'Número de Placa',
        'car_type' => 'Fuente de Energía',
        'primary_vehicle' => 'Vehículo Principal',
        'image' => 'Foto del Vehículo',
        'email' => 'Dirección de Correo Electrónico',
        'password' => 'Contraseña',
        'first_name' => 'Nombre',
        'last_name' => 'Apellido',
        'gender' => 'Genero',
        'dob' => 'Fecha de Nacimiento',
        'country' => 'Pais',
        'address' => 'Direccion',
        'state' => 'Estado',
        'city' => 'Ciudad',
        'zipcode' => 'Codigo Postal',
        'government_issued_id' => 'Identificacion Oficial',
        'bio' => 'Biografia',
        'phone' => 'Número de Teléfono',
        'name' => 'Nombre',
        'title' => 'Título',
        'description' => 'Descripción',
        'name_on_card' => 'Nombre del titular de la tarjeta',
        'card_element' => 'Datos de la tarjeta',
        'pass1' => 'Contraseña actual',
        'pass2' => 'Nueva contraseña',
        'pass3' => 'Confirmación de contraseña',
    ],

];
