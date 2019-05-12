<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 7/1/2018
 * Time: 6:55 PM
 */

namespace App\Jobs\User;


use App\Contract\Job\Job;
use App\Models\User;

/**
 * Class UpdateUserPassword
 * @package App\Jobs\User
 */
class UpdateAvatar implements Job
{
    /**
     * @var
     */
    protected $userId;

    /**
     * @var
     */
    protected $image;

    /**
     * UpdateAvatar constructor.
     * @param $userId
     * @param $image
     */
    public function __construct($userId, $image)
    {
        $this->userId = (int)$userId;
        $this->image = $image;
    }

    /**
     * @return mixed
     */
    public function handle()
    {
        $image_parts = explode(";base64,", $this->image);

        $image_type_aux = explode("image/", $image_parts[0]);

        $image_type = $image_type_aux[1];

        $image_base64 = base64_decode($image_parts[1]);

        $f = finfo_open();
        $mime_type = finfo_buffer($f, $image_base64, FILEINFO_MIME_TYPE);
        $allowedTypes = array('image/png');

        if ( !in_array($mime_type, $allowedTypes))
            die(respond()->fail('uploaded File Type Must be PNG'));


        $fileName = time() . '_' . $this->userId . '_avatar.' . $image_type;
        $file = __DIR__ . '/../../../public/upload/avatar/' . $fileName;

        if (!$success = file_put_contents($file, $image_base64)) {
            die(respond()->fail('upload image is failed'));
        }
        User::updateOneById($this->userId,
            [
                'avatar' => $fileName
            ]
        );

        return 'public/upload/avatar/' . $fileName;
    }

}