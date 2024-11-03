<?php

use App\Enums\UserRoleStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::table('roles', function (Blueprint $table) {
			$table->tinyInteger('status')->default(UserRoleStatus::INACTIVE);

			$table->foreignId('created_by')->constrained('users')->onDelete('restrict')->onUpdate('cascade');
			$table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('restrict')->onUpdate('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::table('roles', function (Blueprint $table) {
			$table->dropColumn('status');
			$table->dropConstrainedForeignId('created_by');
			$table->dropConstrainedForeignId('updated_by');
		});
	}
};
