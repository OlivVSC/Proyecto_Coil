<?php

return [
    'required' => 'El campo :attribute es obligatorio.',
    'email' => 'El campo :attribute debe contener un correo válido.',
    'unique' => 'El valor de :attribute ya está registrado.',
    'confirmed' => 'La confirmación de :attribute no coincide.',
    'min' => ['string' => 'El campo :attribute debe tener al menos :min caracteres.'],
    'max' => ['string' => 'El campo :attribute no debe exceder :max caracteres.'],
    'in' => 'El valor seleccionado para :attribute no es válido.',
    'string' => 'El campo :attribute debe ser texto.',
    'attributes' => [
        'email' => 'correo electrónico',
        'password' => 'contraseña',
        'name' => 'nombre',
        'rol' => 'tipo de cuenta',
        'titulo' => 'título',
        'resumen' => 'resumen',
        'contenido' => 'información completa',
        'imagen' => 'imagen',
        'tipo' => 'categoría',
    ],
];
