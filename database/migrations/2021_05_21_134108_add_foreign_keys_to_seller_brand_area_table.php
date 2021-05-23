<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToSellerBrandAreaTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('seller_brand_area', function(Blueprint $table)
		{
			$table->foreign('seller_id', 'seller_brand_area_ibfk_1')->references('id')->on('sellers')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('area_id', 'seller_brand_area_ibfk_2')->references('id')->on('areas')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('brand_id', 'seller_brand_area_ibfk_3')->references('id')->on('brands')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('seller_brand_area', function(Blueprint $table)
		{
			$table->dropForeign('seller_brand_area_ibfk_1');
			$table->dropForeign('seller_brand_area_ibfk_2');
			$table->dropForeign('seller_brand_area_ibfk_3');
		});
	}
}
