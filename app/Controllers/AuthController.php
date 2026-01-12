<?php

namespace App\Controllers;

use App\Models\KundeModel;
use App\Models\MitarbeiterModel;

class AuthController extends BaseController
{
    public function loginForm()
    {
        return view('auth/login', [
            'error' => session()->getFlashdata('error'),
        ]);
    }

    public function login()
    {
        $email = trim((string) $this->request->getPost('email'));
        $pass  = (string) $this->request->getPost('passwort');

        if ($email === '' || $pass === '') {
            return redirect()->to('/login')->with('error', 'Bitte E-Mail und Passwort ausfüllen.');
        }

        // 1) Kunde prüfen
        $kundeModel = new KundeModel();
        $kunde = $kundeModel->where('email', $email)->first();

        if ($kunde) {
            if (!password_verify($pass, (string) $kunde['passwort'])) {
                return redirect()->to('/login')->with('error', 'E-Mail oder Passwort ist falsch.');
            }

            session()->set([
                'role' => 'kunde',
                'user_id' => $kunde['kid'],
                'name' => $kunde['vorname'] . ' ' . $kunde['nachname'],
                'email' => $kunde['email'],
                'isLoggedIn' => true,
            ]);

            return redirect()->to('/');
        }

        // 2) Mitarbeiter prüfen
        $mitModel = new MitarbeiterModel();
        $mit = $mitModel->where('email', $email)->first();

        if ($mit) {
            if (!password_verify($pass, (string) $mit['passwort'])) {
                return redirect()->to('/login')->with('error', 'E-Mail oder Passwort ist falsch.');
            }

            session()->set([
                'role' => 'mitarbeiter',
                'user_id' => $mit['mid'],
                'name' => $mit['vorname'] . ' ' . $mit['nachname'],
                'email' => $mit['email'],
                'isLoggedIn' => true,
            ]);

            return redirect()->to('/');
        }

        return redirect()->to('/login')->with('error', 'E-Mail oder Passwort ist falsch.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }

    public function registerForm()
    {
        return view('auth/register', [
            'error' => session()->getFlashdata('error'),
            'old'   => session()->getFlashdata('old') ?? [],
        ]);
    }

    public function register()
    {
        $data = [
            'vorname'      => trim((string) $this->request->getPost('vorname')),
            'nachname'     => trim((string) $this->request->getPost('nachname')),
            'geburtsdatum' => (string) $this->request->getPost('geburtsdatum'), // YYYY-MM-DD
            'geschlecht'   => (string) $this->request->getPost('geschlecht'),   // m/w/d
            'strasse'      => trim((string) $this->request->getPost('strasse')),
            'hausnr'       => trim((string) $this->request->getPost('hausnr')),
            'plz'          => trim((string) $this->request->getPost('plz')),
            'ort'          => trim((string) $this->request->getPost('ort')),
            'telefon'      => trim((string) $this->request->getPost('telefon')),
            'email'        => strtolower(trim((string) $this->request->getPost('email'))),
        ];

        $pass1 = (string) $this->request->getPost('passwort');
        $pass2 = (string) $this->request->getPost('passwort2');

        // Minimal-Validierung (kleinschrittig, später verfeinern)
        if ($data['vorname'] === '' || $data['nachname'] === '' || $data['email'] === '' || $pass1 === '') {
            return redirect()->to('/register')->with('error', 'Bitte Pflichtfelder ausfüllen.')->with('old', $data);
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return redirect()->to('/register')->with('error', 'Bitte eine gültige E-Mail eingeben.')->with('old', $data);
        }

        if (!in_array($data['geschlecht'], ['m','w','d'], true)) {
            return redirect()->to('/register')->with('error', 'Bitte ein gültiges Geschlecht wählen.')->with('old', $data);
        }

        if ($pass1 !== $pass2) {
            return redirect()->to('/register')->with('error', 'Die Passwörter stimmen nicht überein.')->with('old', $data);
        }

        if (strlen($pass1) < 8) {
            return redirect()->to('/register')->with('error', 'Passwort muss mindestens 8 Zeichen haben.')->with('old', $data);
        }

        $kundeModel = new \App\Models\KundeModel();

        // Email darf nicht doppelt sein (bei Kunden)
        $existsKunde = $kundeModel->where('email', $data['email'])->first();
        if ($existsKunde) {
            return redirect()->to('/register')->with('error', 'Diese E-Mail ist bereits registriert.')->with('old', $data);
        }

        // Optional: auch gegen Mitarbeiter prüfen (damit Email global eindeutig ist)
        $mitModel = new \App\Models\MitarbeiterModel();
        $existsMit = $mitModel->where('email', $data['email'])->first();
        if ($existsMit) {
            return redirect()->to('/register')->with('error', 'Diese E-Mail ist bereits vergeben.')->with('old', $data);
        }

        $data['passwort'] = password_hash($pass1, PASSWORD_DEFAULT);

        // insert() gibt bei success true/ID, je nach CI4-Version/Settings
        $kundeModel->insert($data);
        $newId = $kundeModel->getInsertID();

        // Direkt einloggen
        session()->set([
            'role' => 'kunde',
            'user_id' => $newId,
            'name' => $data['vorname'] . ' ' . $data['nachname'],
            'email' => $data['email'],
            'isLoggedIn' => true,
        ]);

        return redirect()->to('/');
    }
}