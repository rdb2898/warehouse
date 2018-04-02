<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMirsdetailsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $set_schema_table = 'MIRSDetails';

    /**
     * Run the migrations.
     * @table mirsdetails
     *
     * @return void
     */
    public function up()
    {
      Schema::create('MIRSDetails', function (Blueprint $table) {
        $table->increments('id');
        $table->char('MIRSNo', 7);
        $table->string('ItemCode', 20);
        $table->string('Particulars', 150)->nullable()->default(null);
        $table->string('Unit', 50)->nullable()->default(null);
        $table->bigInteger('Quantity');
        $table->string('Remarks', 100)->nullable()->default(null);
        $table->bigInteger('QuantityValidator')->nullable()->default(null);
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {
       Schema::dropIfExists($this->set_schema_table);
     }
}
