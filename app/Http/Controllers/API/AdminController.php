<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Apprenant;
use App\Models\Employer;
use App\Models\Formateur;
use App\Models\Formation;
use App\Models\Parents;

use App\Models\User;
use Illuminate\Http\Request;
use Validator;
use Vtiful\Kernel\Format;

class AdminController extends BaseController
{
    // AJOUTER EMPLOYER
    public function addEmployer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|alpha|min:3',
            'lastName' => 'required|alpha|min:3',
            'phone' => 'required|digits:8',
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);
        if ($validator->fails()) {
            return $this->sendError('Informations Incorrect', $validator->errors());
        }

        $isExist = User::where('email', $request['email'])->first();
        if ($isExist) {
            return $this->sendError('Utilisateur déja existe', $validator->errors());
        }

        $userInput['role'] = "Employer";
        $userInput['email'] = $request['email'];
        $userInput['name'] = $request['name'];
        $userInput['lastName'] = $request['lastName'];
        $userInput['phone'] = $request['phone'];
        $userInput['password'] = bcrypt($request['password']);
        $user = User::create($userInput);
        $employer_id['employer_id'] = $user->id;
        $employer = Employer::create($employer_id);
        if ($employer)
            return $this->sendResponse($user, 'Employer créer avec success');
        return $this->sendError("Erreur est servenue");

    }


    // MODIFIER UN UTILISATEUR

    // MODIFIER UN UTILISATEUR
    public function modifyUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'name' => 'required|alpha|min:3',
            'lastName' => 'required|alpha|min:3',
            'phone' => 'required|digits:8',
            'email' => 'required|email',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Informations Incorrect', $validator->errors());
        }
        $user = User::where('id', $request['user_id'])->first();

        if ($user) {
            $user->update([
                'name' => $request['name'],
                'lastName' => $request['lastName'],
                'phone' => $request['phone'],
                'email' => $request['email'],

            ]);
            return $this->sendResponse($user, "Utilisateur modifier");
        } else {
            return $this->sendError('Erreur est servenue', $validator->errors());
        }

    }

    // SUPPRIMER UN UTILISATEUR
    public function deleteUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Informations Incorrect', $validator->errors());
        }
        $user = User::where('id', $request['user_id'])->first();
        if ($user) {
            $user->delete();
            return $this->sendResponse([], "utilisateur supprimer");
        } else {
            return $this->sendError('Erreur est servenue', $validator->errors());
        }

    }

    // RECURPER UN UTILISATEUR OU TOUT LES UTILISATEUR
    public function getUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Informations Incorrect', $validator->errors());
        }
        if ($request['user'] == "all") {
            $success = User::all();
            return $this->sendResponse($success, null);
        } else {
            $user = User::where('id', $request['user'])->first();
            return $this->sendResponse($user, null);
        }

    }


    public function searchUserBar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
        ]);


        if ($validator->fails()) {
            return $this->sendError('Informations Incorrect', $validator->errors());
        }

        $search = $request['email'];

        $users = User::where('email', 'LIKE', '%' . $search . '%')
            ->get();
        return $this->sendResponse($users, null);

    }



    // RECÉPURER UN/TOUT LES UTILISATEUR SELON ROLE
    public function getUserByRole(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user' => 'required',
            'role' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendError('Informations Incorrect', $validator->errors());
        }

        switch ($request['role']) {
            case 'Apprenant':
                if ($request['user'] == "all") {
                    // $success = Apprenant::all();
                    $success = User::join('apprenants', 'users.id', '=', 'apprenants.apprenant_id')->get();
                    return $this->sendResponse($success, null);
                } else {
                    $user = Apprenant::where('apprenant_id', $request['user'])->first();
                    return $this->sendResponse($user, null);
                }

            case 'Formateur':
                if ($request['user'] == "all") {
                    //$success = Formateur::all();
                    $success = User::join('formateurs', 'users.id', '=', 'formateurs.formateur_id')->get();

                    return $this->sendResponse($success, null);
                } else {
                    $user = Formateur::where('formateur_id', $request['user'])->first();
                    return $this->sendResponse($user, null);
                }


            case 'Parent':
                if ($request['user'] == "all") {
                    // $success = Parents::all();
                    $success = User::join('parents', 'users.id', '=', 'parents.parent_id')->get();
                    return $this->sendResponse($success, null);
                } else {
                    $user = Parents::where('parent_id', $request['user'])->first();
                    return $this->sendResponse($user, null);
                }

            case 'Employer':
                if ($request['user'] == "all") {
                    $success = Employer::all();
                    return $this->sendResponse($success, null);
                } else {
                    $user = Employer::where('parent_id', $request['user'])->first();
                    return $this->sendResponse($user, null);
                }


            default:
                return $this->sendError('Erreur est servenue', $validator->errors());
        }

    }


    // AJOUTER UNE FORMATION
    public function addFormation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'formation_title' => 'required',
            'formation_price' => 'required',
            'formation_formateur' => 'required',
            'formation_duree' => 'required',
            'formation_picture' => 'required',
            'formation_category' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Informations Incorrect', $validator->errors());
        }
        $isExist = User::where('id', $request['formation_formateur'])->first();
        if (!$isExist) {
            return $this->sendError('Utilisateur non trouvable', $validator->errors());
        }
        $isFormateur = User::where('id', $request['formation_formateur'])->where('role', 'Formateur')->first();
        if (!$isFormateur) {
            return $this->sendError("Vous n'avez pas le deroit", $validator->errors());
        }

        $formationInput['formation_title'] = $request['formation_title'];
        $formationInput['formation_price'] = $request['formation_price'];
        $formationInput['formation_duree'] = $request['formation_duree'];
        $formationInput['formation_formateur'] = $request['formation_formateur'];
        $formationInput['formation_category'] = $request['formation_category'];
        $formationInput['formation_picture'] = $request['formation_picture'];

        $formation = Formation::create($formationInput);
        if ($formation)
            return $this->sendResponse($formation, 'Formation créer avec success');
        return $this->sendError("Erreur est servenue");
    }

    // RECUPPERER LA LISTE DES FORMATIONS
    public function getFormation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'formation' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Informations Incorrect', $validator->errors());
        }
        if ($request['formation'] == "all") {
            $success = Formation::all();
            return $this->sendResponse($success, null);
        } else {
            $formation = Formation::where('id', $request['formation'])->first();
            return $this->sendResponse($formation, null);
        }
    }

    // RECHERCHER UNE FORMATION
    public function searchFormation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Informations Incorrect', $validator->errors());
        }

        $search = $request['title'];

        $users = Formation::where('formation_title', 'LIKE', $search . '%')->get();
        return $this->sendResponse($users, null);
    }

    // MODIFIER UNE FORMATION
    public function modifyFormation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'formation_id' => 'required',
            'formation_title' => 'required',
            'formation_price' => 'required',
            'formation_formateur' => 'required',
            'formation_category' => 'required',
            'formation_duree' => 'required',
            'formation_picture' => 'required',

        ]);
        if ($validator->fails()) {
            return $this->sendError('Informations Incorrect', $validator->errors());
        }

        $isExist = User::where('id', $request['formation_formateur'])->first();
        if (!$isExist) {
            return $this->sendError('Utilisateur non trouvable', $validator->errors());
        }
        $isFormateur = User::where('id', $request['formation_formateur'])->where('role', 'Formateur')->first();

        if (!$isFormateur) {
            return $this->sendError("Utilisateur n'avez pas le deroit", $validator->errors());
        }

        $formation = Formation::where('id', $request['formation_id'])->first();

        if ($formation) {
            $formation->update([
                'formation_title' => $request['formation_title'],
                'formation_price' => $request['formation_price'],
                'formation_formateur' => $request['formation_formateur'],
                'formation_category' => $request['formation_category'],
                'formation_duree' => $request['formation_duree'],
                'formation_picture' => $request['formation_picture'],
            ]);
            return $this->sendResponse($formation, "Formation modifier");
        } else {
            return $this->sendError('Erreur est servenue', $validator->errors());
        }

    }
    // SUPPRIMER UNE FORMATION
    public function deleteFormation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'formation_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Informations Incorrect', $validator->errors());
        }
        $formation = Formation::where('id', $request['formation_id'])->first();
        if ($formation) {
            $formation->delete();
            return $this->sendResponse([], "formation supprimer");
        } else {
            return $this->sendError('Erreur est servenue', $validator->errors());
        }
    }


    public function modifyStatusUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'action' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendError('Informations Incorrect', $validator->errors());
        }
        $user = User::where('id', $request['user_id'])->first();
        if (!$user) {
            return $this->sendError('Utilisateur non trouvable', $validator->errors());
        }
        switch ($user->role) {
            case 'Apprenant':
               // $apprenant = Apprenant::where('apprenant_id', $request['user_id'])->first();
                switch ($request['action']) {
                    case 1:
                        Apprenant::where('apprenant_id', $request['user_id'])->update([
                            'apprenant_statut' => "ACCEPTED",
                        ]);
                        return $this->sendResponse([], "Apprenant Accepté");
                    case 0:
                        Apprenant::where('apprenant_id', $request['user_id'])->update([
                            'apprenant_statut' => "DECLINED",
                        ]);
                        return $this->sendResponse([], "Apprenant Refusé");
                    default:
                        return $this->sendError('Action non acceptable', $validator->errors());
                }
            case 'Formateur':
                $formateur = Formateur::where('formateur_id', $user->id)->first();
                switch ($request['action']) {
                    case 1:
                        $formateur->update([
                            'formateur_statut' => "ACCEPTED",
                        ]);
                        return $this->sendResponse([], "Formateur Accepté");
                    case 0:
                        $formateur->update([
                            'formateur_statut' => "DECLINED",
                        ]);
                        return $this->sendResponse([], "Formateur Refusé");
                    default:
                        return $this->sendError('Action non acceptable', $validator->errors());
                }
            case 'Parent':
                $parent = Parents::where('parent_id', $user->id)->first();
                switch ($request['action']) {
                    case 1:
                        $parent->update([
                            'parent_statut' => "ACCPETED",
                        ]);
                        return $this->sendResponse([], "Parent Accepté");
                    case 0:
                        $parent->update([
                            'parent_statut' => "DECLINED",
                        ]);
                        return $this->sendResponse([], "Parent Refusé");
                    default:
                        return $this->sendError('Action non acceptable', $validator->errors());
                }
            default:
                return $this->sendError('Erreur est servenue', $validator->errors());
        }
    }

    public function declineRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'formation_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Informations Incorrect', $validator->errors());
        }
        $formation = Formation::where('id', $request['formation_id'])->first();
        if ($formation) {
            $formation->delete();
            return $this->sendResponse([], "formation supprimer");
        } else {
            return $this->sendError('Erreur est servenue', $validator->errors());
        }
    }
}
