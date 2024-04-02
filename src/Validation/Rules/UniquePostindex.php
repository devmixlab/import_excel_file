<?php
namespace App\Validation\Rules;

use Respect\Validation\Rules\AbstractRule;
use App\Database\SqlBuilder\Param;
use App\Models\Postindex;
use PDO;

final class UniquePostindex extends AbstractRule
{
    public function validate($input): bool
    {
        $count = (bool)Postindex::initSelect("COUNT(*)")->append("
            WHERE `post_code_of_post_office` = :post_code_of_post_office
        ", [
            new Param(':post_code_of_post_office', $input, PDO::PARAM_INT)
        ])->fetch(PDO::FETCH_COLUMN);

        return !$count;
    }
}