<?php

namespace Antriver\LaravelSiteUtils\Users;

use Illuminate\Database\Eloquent\Model as EloquentModel;

/**
 * Class PasswordHasher
 *
 * Some users still have their passwords stored as insecure hashes.
 * This class verifies passwords match the stored hash and upgraded saved passwords to stronger
 * algorithms as necessary.
 */
class PasswordHasher
{
    /**
     * @var int
     */
    private $algorithm = PASSWORD_DEFAULT;

    /**
     * @var int
     */
    private $cost = 10;

    /**
     * Check if the given password matches the stored hash.
     * If it matches, re-hashes the stored password if ncessary.
     *
     * @param string        $string
     * @param EloquentModel $model
     * @param string        $column
     * @param string        $oldFormat What algorithm was used for the old hash?
     *
     * @return bool
     */
    public function verify(
        $string,
        EloquentModel $model,
        $column,
        $oldFormat = 'md5'
    ) {
        $savedHash = $model->{$column};

        // If password is old format
        if (substr($savedHash, 0, 1) !== '$') {
            if ($this->generateOldHash($string, $oldFormat) == $savedHash) {
                $this->updatedSavedHash($string, $model, $column);

                return true;
            }

            // Incorrect password
            return false;
        }

        // Password is in the new format
        if (password_verify($string, $savedHash)) {
            if (password_needs_rehash($savedHash, $this->algorithm, ['cost' => $this->cost])) {
                $this->updatedSavedHash($string, $model, $column);
            }

            return true;
        }

        // Incorrect password
        return false;
    }

    /**
     * Generates an old hash to validate a password if it has not been updated yet.
     *
     * @param string $string
     * @param string $oldFormat
     *
     * @return string
     */
    private function generateOldHash($string, $oldFormat = 'md5')
    {
        switch ($oldFormat) {
            case 'md5':
                return md5($string);
                break;

            case 'sha1':
                return sha1($string);
                break;

            case 'plain':
            default:
                return $string;
                break;
        }
    }

    /**
     * Generates a hash of the given string.
     *
     * @param string $string
     *
     * @return string
     */
    public function generateHash($string)
    {
        return password_hash($string, $this->algorithm, ['cost' => $this->cost]);
    }

    /**
     * Updates the stored password to a new hash if required.
     *
     * @param string        $string
     * @param EloquentModel $model
     * @param string        $column
     */
    private function updatedSavedHash($string, EloquentModel $model, $column)
    {
        // Generate a new hash
        $newHash = $this->generateHash($string);

        $model->forceFill(
            [
                $column => $newHash,
            ]
        );
        $model->save();
    }
}
