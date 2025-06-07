<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Hotfix_1_0_2 extends Migration
{
    public function up()
    {
        // Change bad ISBN
        $regex = '/\A[' . config('App')->permittedURIChars . ']+\z/iu';
        $books = $this->db->query('select * from book;')->getResultObject();
        $i = 1;
        foreach ($books as $book) {
            if (preg_match($regex, $book->isbn) !== 1) {
                $sql = 'insert into book (isbn, title, description, publisher, theme, `year`, copy, reference, comment)'
                    . "values ('todo_isbn". $i . "','" . $book->title . "','" . $book->description . "','" . $book->publisher
                    . "','" . $book->theme . "','" . $book->year . "','" . $book->copy . "','" . $book->reference . "','" . $book->comment . "');";
                $this->db->query($sql);
                $this->db->query("update `write` set book = 'todo_isbn". $i . "' where book = '" . $book->isbn . "';");
                $this->db->query("update borrow set book = 'todo_isbn". $i . "' where book = '" . $book->isbn . "';");
                $this->db->query("delete from book where isbn = '" . $book->isbn . "';");
                if ($book->year == null) {
                    $this->db->query("update book set `year` = null where isbn = 'todo_isbn" . $i . "';");
                }
                $i++;
            }
        }
    }

    public function down()
    {
        // NO-OP
    }
}
