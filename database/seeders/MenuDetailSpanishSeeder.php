<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * @deprecated Use MenuDetailFooterSeeder instead.
 * This seeder is kept for backward compatibility and now delegates to MenuDetailFooterSeeder,
 * which seeds footer menu details for all languages (English, French, Arabic, Spanish, Tagalog,
 * Chinese, Hindi, Urdu, Russian, Ukrainian) as defined in the languages table.
 */
class MenuDetailSpanishSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(MenuDetailFooterSeeder::class);
    }
}
