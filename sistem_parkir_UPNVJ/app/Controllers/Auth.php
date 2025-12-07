<?php namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PetugasModel;

class Auth extends BaseController
{
    public function index()
    {
        // Cek jika sudah login, langsung lempar ke dashboard
        if(session()->get('logged_in')){
            return redirect()->to('/dashboard');
        }
        return view('login');
    }

    public function loginProcess()
    {
        $session = session();
        $model = new PetugasModel();
        
        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');
        
        $data = $model->where('username', $username)->first();
        
        if($data){
            if($data['password'] == $password){ // Nanti ganti password_verify jika sudah hash
                $ses_data = [
                    'id_petugas' => $data['id_petugas'],
                    'nama'       => $data['nama'],
                    'id_shift'   => $data['id_shift'],
                    'logged_in'  => TRUE
                ];
                $session->set($ses_data);
                
                // SUKSES: Kirim flashdata 'login_sukses'
                return redirect()->to('/dashboard')->with('login_sukses', 'Selamat Datang, ' . $data['nama']);
            }
        }
        
        // GAGAL: Kembalikan ke halaman sebelumnya (Status/Index) dengan pesan error
        return redirect()->back()->with('login_gagal', 'Username atau Password Salah!');
    }

    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to('/');
    }
}
