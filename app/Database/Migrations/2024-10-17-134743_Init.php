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

    protected function createTable(): void
    {
        // Create table publisher
        $fields = [
            'id' => [
                'type' => 'INT',
                'constraint' => 9,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 512,
            ]
        ];
        $this->forge->addField($fields);
        $this->forge->addPrimaryKey('id', 'pk_publisher');
        $this->forge->createTable('publisher');
        // Create table author
        $fields = [
            'id' => [
                'type' => 'INT',
                'constraint' => 9,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'username' => [
                'type' => 'VARCHAR',
                'constraint' => 512,
            ]
        ];
        $this->forge->addField($fields);
        $this->forge->addPrimaryKey('id', 'pk_author');
        $this->forge->createTable('author');
        // Create table book
        $fields = [
            'isbn' => [
                'type' => 'VARCHAR',
                'constraint' => 13,
                'unique' => true
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 1024,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'publisher' => [
                'type' => 'INT',
                'unsigned' => true,
                'constraint' => 9,
                'null' => true
            ],
            'collection' => [
                'type' => 'VARCHAR',
                'constraint' => 512,
                'null' => true
            ]
        ];
        $this->forge->addField($fields);
        $this->forge->addPrimaryKey('isbn', 'pk_book');
        $this->forge->addForeignKey('publisher', 'publisher', 'id', 'CASCADE', 'SET_NULL', 'fk_book_publisher');
        $this->forge->createTable('book');
        // Create table write
        $fields = [
            'author' => [
                'type' => 'INT',
                'constraint' => 9,
                'unsigned' => true
            ],
            'book' => [
                'type' => 'VARCHAR',
                'constraint' => 13
            ],
            'main' => [
                'type' => 'BOOLEAN',
                'default' => false
            ]
        ];
        $this->forge->addField($fields);
        $this->forge->addPrimaryKey(['author', 'book'], 'pk_write');
        $this->forge->addForeignKey('author', 'author', 'id', 'CASCADE', 'CASCADE', 'fk_write_author');
        $this->forge->addForeignKey('book', 'book', 'isbn', 'CASCADE', 'CASCADE', 'fk_write_book');
        $this->forge->createTable('write');
    }

    protected function dropTable(): void
    {
        $this->forge->dropTable('write', true, true);
        $this->forge->dropTable('author', true, true);
        $this->forge->dropTable('book', true, true);
        $this->forge->dropTable('publisher', true, true);
    }

    protected function setData(): void {
        // Create superadmin user
        $users = auth()->getProvider();
        $user = new User([
            'username' => 'admin',
            'email'    => 'todo@replace.com',
            'password' => 'admin',
        ]);
        $users->save($user);
        // Add group
        $user = $users->findById($users->getInsertID());
        $user->syncGroups('superadmin');
        // Disabled register and magic link
        service('settings')->set('Auth.allowRegistration', false);
        service('settings')->set('Auth.allowMagicLinkLogins', false);
        // Default Publisher and Author
        $defaultPublisher = lang('Init.default.publisher');
        $this->db->query("Insert into publisher(name) values ('$defaultPublisher')");
        $defaultAuthor = lang('Init.default.author');
        $this->db->query("Insert into author(username) values ('$defaultAuthor')");
    }
}
