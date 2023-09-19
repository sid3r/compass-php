<?php

use Illuminate\Database\Seeder;

class ConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      // config items
      $config = [
        // company
        [
          'name' => 'company_logo',
          'group' => 'company',
          'value' => 'logo.png',
        ],
        [
          'name' => 'company_name',
          'group' => 'company',
          'value' => 'Compass',
        ],
        [
          'name' => 'company_sector',
          'group' => 'company',
          'value' => 'Gestion des stocks multi-dépôts.',
        ],
        [
          'name' => 'company_description',
          'group' => 'company',
          'value' => '',
        ],
        [
          'name' => 'company_address',
          'group' => 'company',
          'value' => '',
        ],
        [
          'name' => 'company_region',
          'group' => 'company',
          'value' => 'Béjaïa',
        ],
        [
          'name' => 'company_country',
          'group' => 'company',
          'value' => 'Algérie',
        ],
        [
          'name' => 'company_tel',
          'group' => 'company',
          'value' => '034 00 00 00',
        ],
        [
          'name' => 'company_fax',
          'group' => 'company',
          'value' => '',
        ],
        [
          'name' => 'company_email',
          'group' => 'company',
          'value' => 'contact@compassapp.com',
        ],
        // invoicing
        [
          'name' => 'company_nif',
          'group' => 'invoicing',
          'value' => '',
        ],
        [
          'name' => 'company_nis',
          'group' => 'invoicing',
          'value' => '',
        ],
        [
          'name' => 'company_rc',
          'group' => 'invoicing',
          'value' => '',
        ],
        [
          'name' => 'company_ai',
          'group' => 'invoicing',
          'value' => '',
        ],
        [
          'name' => 'company_rib',
          'group' => 'invoicing',
          'value' => '',
        ],
        [
          'name' => 'display_stamp',
          'group' => 'invoicing',
          'value' => 'yes',
        ],
        // currency
        [
          'name' => 'currency_name',
          'group' => 'currency',
          'value' => 'DZD',
        ],
        [
          'name' => 'currency_separator',
          'group' => 'currency',
          'value' => ',',
        ],
      ];

      // insert
      foreach($config as $item) {
        DB::table('config')->insert($item);
      }
    }
}
