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
            'theme' => [
                'type' => 'VARCHAR',
                'constraint' => 512,
                'null' => true
            ],
            'year' => [
                'type' => 'INT',
                'constraint' => 9,
                'null' => true
            ],
            'copy' => [
                'type' => 'INT',
                'constraint' => 9,
                'default' => 1
            ],
            'reference' => [
                'type' => 'VARCHAR',
                'constraint' => 512,
                'null' => true
            ],
            'comment' => [
                'type' => 'TEXT',
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
            ],
            'role' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true
            ]
        ];
        $this->forge->addField($fields);
        $this->forge->addPrimaryKey(['author', 'book'], 'pk_write');
        $this->forge->addForeignKey('author', 'author', 'id', 'CASCADE', 'CASCADE', 'fk_write_author');
        $this->forge->addForeignKey('book', 'book', 'isbn', 'CASCADE', 'CASCADE', 'fk_write_book');
        $this->forge->createTable('write');
        // Create table member
        $fields = [
            'id' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'unique' => true
            ],
            'firstname' => [
                'type' => 'VARCHAR',
                'constraint' => 255
            ],
            'lastname' => [
                'type' => 'VARCHAR',
                'constraint' => 255
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 512
            ],
            'created_at' => [
                'type' => 'DATE',
            ]
        ];
        $this->forge->addField($fields);
        $this->forge->addPrimaryKey('id', 'pk_member');
        $this->forge->createTable('member');
        // Create table borrow
        $fields = [
            'member' => [
                'type' => 'VARCHAR',
                'constraint' => 20
            ],
            'book' => [
                'type' => 'VARCHAR',
                'constraint' => 13
            ],
            'out_date' => [
                'type' => 'DATE'
            ],
            'return_date' => [
                'type' => 'DATE',
                'null' => true
            ],
            'delay' => [
                'type' => 'INT',
                'constraint' => 9,
            ]
        ];
        $this->forge->addField($fields);
        $this->forge->addPrimaryKey('id', 'pk_borrow');
        $this->forge->addForeignKey('member', 'member', 'id', 'CASCADE', 'CASCADE', 'fk_borrow_author');
        $this->forge->addForeignKey('book', 'book', 'isbn', 'CASCADE', 'CASCADE', 'fk_borrow_book');
        $this->forge->createTable('borrow');
    }

    protected function dropTable(): void
    {
        $this->forge->dropTable('borrow', true, true);
        $this->forge->dropTable('member', true, true);
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
