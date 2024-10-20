<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Shield\Entities\User;

class Init extends Migration
{
    public function up(): void
    {
        $this->createTable();
        $this->setData();
    }

    public function down(): void
    {
        $this->dropTable();
    }

    protected function createTable() {

    }

    protected function dropTable() {

    }

    protected function setData(): void {
        // Création du compte admin
        $users = auth()->getProvider();
        $user = new User([
            'username' => 'admin',
            'email'    => 'todo@replace.com',
            'password' => 'admin',
        ]);
        $users->save($user);
        // Ajout des permissions
        $user = $users->findById($users->getInsertID());
        $user->syncGroups('superadmin');
        // Bloque la création de compte et l'utilisation de lien magique
        service('settings')->set('Auth.allowRegistration', false);
        service('settings')->set('Auth.allowMagicLinkLogins', false);
    }
}
