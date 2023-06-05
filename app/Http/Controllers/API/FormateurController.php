<?php

namespace App\Http\Controllers\api;


use Illuminate\Http\Request;

use App\Models\Formation;

use Validator;


class FormateurController extends BaseController
{



    public function getformationByformateur(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'formateur_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Informations incorrect...!', $validator->errors());
        }

        $formateur = $request['formateur_id'];
        $formation = Formation::where('formation_formateur', $formateur)->get();
        return $this->sendResponse($formation, null);

    }
}

