<?php

namespace Tests\Feature\Requests\User;

use App\Http\Requests\User\UserStoreRequest;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @internal
 */
class UserStoreRequestTest extends TestCase
{
    #[Test]
    public function itShouldFailWhenRequiredFieldsAreMissing()
    {
        $data = [];

        $validator = Validator::make($data, (new UserStoreRequest())->rules());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
        $this->assertArrayHasKey('username', $validator->errors()->toArray());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
        $this->assertArrayHasKey('terms_and_privacy_agreement', $validator->errors()->toArray());
    }

    #[Test]
    public function itShouldFailWithInvalidEmail()
    {
        $data = $this->getBaseValidData();
        $data['email'] = 'not-an-email';

        $validator = Validator::make($data, (new UserStoreRequest())->rules());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }

    #[Test]
    public function itShouldFailWithWeakPassword()
    {
        $data = $this->getBaseValidData();
        $data['password'] = $data['password_confirm'] = 'abcdefgh'; // só letras

        $validator = Validator::make($data, (new UserStoreRequest())->rules());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    #[Test]
    public function itShouldFailWhenPasswordsDoNotMatch()
    {
        $data = $this->getBaseValidData();
        $data['password_confirm'] = 'different123';

        $validator = Validator::make($data, (new UserStoreRequest())->rules());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('password_confirm', $validator->errors()->toArray());
    }

    #[Test]
    public function itShouldFailWhenTermsAreNotAccepted()
    {
        $data = $this->getBaseValidData();
        $data['terms_and_privacy_agreement'] = false;

        $validator = Validator::make($data, (new UserStoreRequest())->rules());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('terms_and_privacy_agreement', $validator->errors()->toArray());
    }

    private function getBaseValidData(): array
    {
        return [
            'name' => 'Murilo',
            'surname' => 'Figueiredo',
            'number' => '1234567890123',
            'email' => 'murilo@example.com',
            'password' => 'password123',
            'password_confirm' => 'password123',
            'terms_and_privacy_agreement' => true,
            'remember' => '1',
        ];
    }
}
