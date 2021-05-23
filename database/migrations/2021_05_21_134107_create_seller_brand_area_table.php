<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSellerBrandAreaTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('seller_brand_area', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('seller_id')->index('seller_id');
			$table->integer('brand_id')->index('brand_id');
			$table->integer('area_id')->index('area_id');
			$table->integer('city_id');
			$table->enum('status', ['0','1'])->default('1')->comment('0 for Inactive and 1 for Active');
			$table->timestamp('created_at')->useCurrent();
			$table->timestamp('updated_at')->useCurrent();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('seller_brand_area');
	}
}
