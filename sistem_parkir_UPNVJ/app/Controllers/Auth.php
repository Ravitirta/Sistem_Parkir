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
        
        // Ambil input dari form
        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');
        
        // Cari data berdasarkan username
        $data = $model->where('username', $username)->first();
        
        if($data){
            // Cek Password
            if($data['password'] == $password){
                
                // Set Session Data
                $ses_data = [
                    'id_petugas' => $data['id_petugas'],
                    'nama'       => $data['nama'],
                    'id_shift'   => $data['id_shift'],
                    'logged_in'  => TRUE
                ];
                $session->set($ses_data);
                
                return redirect()->to('/dashboard');
            }else{
                $session->setFlashdata('msg', 'Password Salah');
                return redirect()->to('/');
            }
        }else{
            $session->setFlashdata('msg', 'Username tidak ditemukan');
            return redirect()->to('/');
        }
    }

    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to('/');
    }
}
