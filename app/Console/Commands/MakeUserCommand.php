<?php

namespace App\Console\Commands;

use App\Enums\UserStatus;
use App\Models\User;
use App\Providers\AuthServiceProvider;
use App\Repositories\UserRepository;
use App\RoutePaths\Admin\Auth\AuthRoutePath;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class MakeUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make a new user';

    public function __construct(
        private UserRepository $userRepository,
    ) {
        parent::__construct();
    }

    /**
     * Get user input attributes.
     */
    protected function getUserAttributes(): array
    {
        return [
            'name' => text(
                label: 'Name',
                required: true,
            ),
            'email' => text(
                label: 'Email',
                required: true,
                validate: fn (string $email): ?string => match (true) {
                    !filter_var($email, FILTER_VALIDATE_EMAIL) => 'The email must be valid.',
                    (bool) $this->user()->getFirstWhere('email', $email) => 'A user with this email already exists',
                    default => null,
                },
            ),
            'password' => password(
                    label: 'Password',
                    required: true,
                    validate: fn (string $password): ?string => match (true) {
                        str()->of($password)->length() < 8 => 'Password length must at least be 8 characters long.',
                        default => null,
                    },
            ),
        ];
    }

    /**
     * Admin user service.
     */
    protected function user(): UserRepository
    {
        return $this->userRepository;
    }

    /**
     * Create admin user.
     */
    protected function createUser(): User
    {
        $user = $this->user()->create([
            ...$this->getUserAttributes(),
            'created_by' => AuthServiceProvider::SUPER_ADMIN,
            'status' => UserStatus::ACTIVE,
        ]);

        if (AuthServiceProvider::SUPER_ADMIN === $user->id) {
            Artisan::call('db:seed', ['--class' => 'UserRoleSeeder']);
            $user->assignRole(AuthServiceProvider::SUPER_ADMIN);
        }

        $user->markEmailAsVerified();

        return $user;
    }

    /**
     * Print success message.
     */
    protected function sendSuccessMessage(): void
    {
        $loginUrl = route(AuthRoutePath::LOGIN);

        $this->components->info("Success! You may now log in at {$loginUrl}");
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->createUser();
        $this->sendSuccessMessage();

        return static::SUCCESS;
    }
}
