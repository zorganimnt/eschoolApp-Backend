<?php

namespace App\Http\Controllers\api;
use App\Http\Controllers\Controller;
use App\Models\Formation;
use App\Models\Cours;
use Illuminate\Http\Request;
use Validator;


class CoursController extends BaseController
{

        // AJOUTER UN COURS
        public function addCours(Request $request)
        {
            $validator = Validator::make($request->all(), [
                'formation_title' => 'required',
                'cours_title' => 'required',
                'cours_description' => 'required',
                'cours_support' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Informations Incorrect', $validator->errors());
            }

            $isExist = Formation::where('id', $request['formation_title'])->first();
            if (!$isExist) {
                return $this->sendError('Formation non trouvable', $validator->errors());
            }

            $coursInput['formation_title'] = $request['formation_title'];
            $coursInput['cours_title'] = $request['cours_title'];
            $coursInput['cours_description'] = $request['cours_description'];
            $coursInput['cours_support'] = $request['cours_support'];

            $cours = Cours::create($coursInput);
            if ($cours)
                return $this->sendResponse($cours, 'Cours ajoutée avec success');

            return $this->sendError("Erreur est servenue");

        }

         //MODIFIER COURS

        public function modifyCours(Request $request)
        {
            $validator = Validator::make($request->all(), [
                'cours_id' => 'required',
                'formation_title' => 'required',
                'cours_title' => 'required',
                'cours_description' => 'required',
                'cours_support' => 'required',

            ]);
            if ($validator->fails()) {
                return $this->sendError('Informations Incorrect', $validator->errors());
            }

            $isExist = Formation::where('id', $request['formation_title'])->first();
            if (!$isExist) {
                return $this->sendError('Formation non trouvable', $validator->errors());
            }

            $cours = Cours::where('id', $request['cours_id'])->first();

            if ($cours) {
                $cours->update([
                    'formation_title' => $request['formation_title'],
                    'cours_title' => $request['cours_title'],
                    'cours_description' => $request['cours_description'],
                    'cours_support' => $request['cours_support'],
                ]);
                return $this->sendResponse($cours, "Cours modifier avec succès");
            } else {
                return $this->sendError('Erreur est servenue', $validator->errors());
            }

        }

        // DELETE COURS

        public function deleteCours(Request $request)
        {
            $validator = Validator::make($request->all(), [
                'cours_id' => 'required',
            ]);
            if ($validator->fails()) {
                return $this->sendError('Informations Incorrect', $validator->errors());
            }
            $cours = Cours::where('id', $request['cours_id'])->first();
            if ($cours) {
                $cours->delete();
                return $this->sendResponse([], "cours supprimer");
            } else {
                return $this->sendError('Erreur est servenue', $validator->errors());
            }
        }


        // GET COURS

        public function getCours(Request $request)
        {
            $validator = Validator::make($request->all(), [
                'cours' => 'required',
            ]);
            if ($validator->fails()) {
                return $this->sendError('Informations Incorrect', $validator->errors());
            }
            if ($request['cours'] == "all") {
                $success = Cours::all();
                return $this->sendResponse($success, null);
            } else {
                $cours = Cours::where('id', $request['cours'])->first();
                return $this->sendResponse($cours, null);
            }
        }
        public function getcoursByformationid(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'formation_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Informations incorrect...!', $validator->errors());
        }

        $formation= $request['formation_id'];
        $cours = cours::where('formation_title', $formation)->get();
        return $this->sendResponse($cours, null);

    }
}

