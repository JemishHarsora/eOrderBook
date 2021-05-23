<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->text('verification_code')->nullable();
            $table->text('new_email_verificiation_code')->nullable();
            $table->string('user_type', 10)->default('customer');
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->string('avatar', 256)->nullable();
            $table->string('avatar_original', 256)->nullable();
            $table->string('address', 300)->nullable();
            $table->string('country', 30)->nullable();
            $table->string('city', 30)->nullable();
            $table->integer('area')->nullable();
            $table->string('contact_name', 255)->nullable();
            $table->string('business_category', 255);
            $table->string('licence_no', 50)->nullable();
            $table->string('gst_no', 255)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('phone', 20)->nullable();
            $table->double('balance', 20, 2)->default(0.00);
            $table->boolean('banned')->default('0');
            $table->integer('referred_by')->nullable();
            $table->integer('created_by')->nullable();
            $table->string('provider_id', 50)->nullable();
            $table->string('referral_code', 255)->nullable();
            $table->integer('customer_package_id')->nullable();
            $table->integer('remaining_uploads')->nullable()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users');
    }
}
