<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\GoogleDriveService;
use App\Models\Article;
use App\Models\Scholarship;

$service = app(GoogleDriveService::class);

// Manual recovery for Article 6 (from article_info_utf8.txt)
$a6 = Article::find(6);
if ($a6) {
    echo "Recovering Article 6...\n";
    $a6->banner = "https://lh3.googleusercontent.com/d/1W7gDZTpd-dr_FJp4NyD1K5cc_RKzlfpV";
    $a6->images = [
        "https://lh3.googleusercontent.com/d/1W7gDZTpd-dr_FJp4NyD1K5cc_RKzlfpV",
        "https://lh3.googleusercontent.com/d/12MGJcz1-30gKgE7coOxJH7-xJChn39Cn",
        "https://lh3.googleusercontent.com/d/1Rd6gz5MwlOG9uKOFcQx5ezRtxsQNHklv",
        "https://lh3.googleusercontent.com/d/1wwTkKdLmQ8THxDB9YYusoQdr56y4TUF3",
        "https://lh3.googleusercontent.com/d/1HSadaZgje01U0TN6FvBbVOKMz-Hnetf_"
    ];
    $a6->save();
}

echo "Recovery Done!\n";
