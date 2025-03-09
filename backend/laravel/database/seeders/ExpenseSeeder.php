<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Expense;
use App\Models\Company; // Falls Unternehmen existieren
use Carbon\Carbon;
use Faker\Factory as Faker;

class ExpenseSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Standard-Firma auswählen (falls es mehrere gibt)
        $company = Company::first(); // Oder Company::inRandomOrder()->first();
        if (!$company) {
            throw new \Exception(" Keine Firma in der Datenbank. Führe `php artisan db:seed --class=CompanySeeder` zuerst aus!");
        }

        // Kategorien mit typischen Beträgen
        $categories = [
            'Miete' => 2000,
            'Löhne' => 4500,
            'Leasing' => 1500,
            'Software' => 300,
            'Büromaterial' => 150,
            'Reisekosten' => 600,
            'Sonstiges' => 250
        ];

        // Monatliche Fixkosten (regelmäßig wiederkehrende Ausgaben)
        foreach ($categories as $category => $amount) {
            for ($i = 0; $i < 6; $i++) { // Letzte 6 Monate generieren
                Expense::create([
                    'company_id' => $company->id, // **Company-ID hinzufügen**
                    'amount' => $amount,
                    'category' => $category,
                    'description' => $category . ' für ' . Carbon::now()->subMonths($i)->format('F Y'),
                    'date' => Carbon::now()->subMonths($i)->startOfMonth(),
                    'recurring' => true,
                    'attachment' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }

        // Einmalige Ausgaben (z. B. Büroausstattung, Events)
        for ($i = 0; $i < 10; $i++) {
            Expense::create([
                'company_id' => $company->id, // **Company-ID hinzufügen**
                'amount' => $faker->randomFloat(2, 50, 2000),
                'category' => $faker->randomElement(['Marketing', 'IT-Wartung', 'Schulung', 'Kundengeschenke']),
                'description' => $faker->sentence(4),
                'date' => $faker->dateTimeBetween('-6 months', 'now'),
                'recurring' => false,
                'attachment' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        echo "Seeder erfolgreich ausgeführt! 20 Ausgaben für Firma '{$company->name}' erstellt.\n";
    }
}
