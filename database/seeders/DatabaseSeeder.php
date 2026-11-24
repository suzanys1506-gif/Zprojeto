<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Book;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminEmail = config('library.admin.email');
        $adminPassword = config('library.admin.password');

        if (filled($adminEmail) !== filled($adminPassword)) {
            throw new \InvalidArgumentException('Both ADMIN_EMAIL and ADMIN_PASSWORD must be configured to seed the administrator.');
        }

        if (filled($adminEmail)) {
            User::firstOrCreate(['email' => $adminEmail], [
                'name' => 'Admin Entrelinhas', 'password' => $adminPassword, 'role' => 'admin',
            ]);
        }

        foreach ([
            ['A hora da estrela','Clarice Lispector','Rocco','Romance',1977,'9788532508126',4],
            ['Capitães da Areia','Jorge Amado','Companhia das Letras','Ficção brasileira',1937,'9788535914063',3],
            ['Quarto de despejo','Carolina Maria de Jesus','Ática','Memórias',1960,'9788508171279',2],
            ['O pequeno príncipe','Antoine de Saint-Exupéry','Agir','Literatura',1943,'9788522005233',5],
            ['Torto arado','Itamar Vieira Junior','Todavia','Romance',2019,'9786580309313',3],
            ['A vida invisível de Eurídice Gusmão','Martha Batalha','Companhia das Letras','Ficção brasileira',2016,'9788535927445',2],
            ['Ideias para adiar o fim do mundo','Ailton Krenak','Companhia das Letras','Ensaios',2019,'9788535932416',3],
            ['Olhos d’água','Conceição Evaristo','Pallas','Contos',2014,'9788534705259',2],
        ] as [$title,$author,$publisher,$category,$year,$isbn,$copies]) {
            Book::firstOrCreate(['isbn'=>$isbn], ['title'=>$title,'author'=>$author,'publisher'=>$publisher,'category'=>$category,'publication_year'=>$year,'copies'=>$copies,'available_copies'=>$copies]);
        }
    }
}
