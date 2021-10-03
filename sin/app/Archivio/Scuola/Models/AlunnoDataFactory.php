<?php

namespace App\Scuola\Models;

use http\Env\Request;

class AlunnoDataFactory
{
    public function fromRequest(Request $request): CustomerData {
        return new CustomerData([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'birth_date' => Carbon::make(
                $request->get('birth_date')
            ),
        ]);
    }

}