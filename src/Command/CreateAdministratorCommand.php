<?php

namespace App\Command;

use App\Entity\Administrator;
use App\Service\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(name: 'app:create-admin', description: 'Create a new admin.')]
class CreateAdministratorCommand extends Command
{
    private readonly ValidatorInterface $validator;

    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly UserManager $userManager)
    {
        $this->validator = Validation::createValidator();
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Create a new admin.')
            ->setDefinition([
                new InputArgument('email', InputArgument::REQUIRED, 'The email'),
                new InputArgument('password', InputArgument::REQUIRED, 'The password'),
            ]);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');

        $administrator = new Administrator();
        $administrator->setEmail($email);
        $administrator->setPlainPassword($password);
        $administrator->setEnabled(true);
        $this->userManager->updatePassword($administrator);
        $this->entityManager->persist($administrator);
        $this->entityManager->flush();

        $output->writeln(sprintf('Created admin <comment>%s</comment>', $email));

        return Command::SUCCESS;
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $questions = [];

        if (!$input->getArgument('email')) {
            $question = new Question('Please choose an email:');
            $question->setValidator(function ($email) {
                if (empty($email)) {
                    throw new Exception('Email can not be empty');
                }
                $constraintViolationList = $this->validator->validate($email, [
                    new NotBlank(),
                    new NotNull(),
                    new Email(null, null, Email::VALIDATION_MODE_HTML5),
                ]);
                if (0 !== count($constraintViolationList)) {
                    throw new Exception('Email must be valid (ex: john.doe@gmail.com)');
                }

                return $email;
            });
            $questions['email'] = $question;
        }

        if (!$input->getArgument('password')) {
            $question = new Question('Please choose a password:');
            $question->setValidator(function ($password) {
                if (empty($password)) {
                    throw new Exception('Password can not be empty');
                }
                $constraintViolationList = $this->validator->validate($password, [
                    new NotBlank(),
                    new NotNull(),
                    new Length(['min' => 6]),
                ]);
                if (0 !== count($constraintViolationList)) {
                    throw new Exception('Password must contain at least 6 characters');
                }

                return $password;
            });
            $question->setHidden(true);
            $questions['password'] = $question;
        }

        foreach ($questions as $name => $question) {
            $answer = $this->getHelper('question')->ask($input, $output, $question);
            $input->setArgument($name, $answer);
        }
    }
}
