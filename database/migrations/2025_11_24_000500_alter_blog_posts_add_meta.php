<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            if (!Schema::hasColumn('blog_posts', 'seo_title')) {
                $table->string('seo_title')->nullable()->after('image_path');
            }
            if (!Schema::hasColumn('blog_posts', 'seo_description')) {
                $table->string('seo_description', 500)->nullable()->after('seo_title');
            }
            if (!Schema::hasColumn('blog_posts', 'seo_image')) {
                $table->string('seo_image')->nullable()->after('seo_description');
            }
            if (!Schema::hasColumn('blog_posts', 'indexable')) {
                $table->boolean('indexable')->default(true)->after('seo_image');
            }
            if (!Schema::hasColumn('blog_posts', 'tags')) {
                $table->string('tags')->nullable()->after('indexable');
            }
            if (!Schema::hasColumn('blog_posts', 'gallery')) {
                $table->json('gallery')->nullable()->after('tags');
            }
            if (!Schema::hasColumn('blog_posts', 'allow_comments')) {
                $table->boolean('allow_comments')->default(true)->after('gallery');
            }
        });
    }

    public function down(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            if (Schema::hasColumn('blog_posts', 'seo_title')) $table->dropColumn('seo_title');
            if (Schema::hasColumn('blog_posts', 'seo_description')) $table->dropColumn('seo_description');
            if (Schema::hasColumn('blog_posts', 'seo_image')) $table->dropColumn('seo_image');
            if (Schema::hasColumn('blog_posts', 'indexable')) $table->dropColumn('indexable');
            if (Schema::hasColumn('blog_posts', 'tags')) $table->dropColumn('tags');
            if (Schema::hasColumn('blog_posts', 'gallery')) $table->dropColumn('gallery');
            if (Schema::hasColumn('blog_posts', 'allow_comments')) $table->dropColumn('allow_comments');
        });
    }
};
