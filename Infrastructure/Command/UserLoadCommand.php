<?php

namespace Infrastructure\Command;

use Application\AuthService;
use Application\Dto\UserRegisterDto;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UserLoadCommand extends Command
{
    public const COMMAND_NAME = 'user:load';

    public function __construct(
        private AuthService $authService
    )
    {
        parent::__construct();
    }


    protected function configure()
    {
        $this->setName(self::COMMAND_NAME)
            ->setDescription('Execute user load from file')
            ->addArgument('filename', InputArgument::REQUIRED)
            ->addArgument('count', InputArgument::REQUIRED);
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $fileName = $input->getArgument('filename');
        if (empty($fileName) === true) {
            $io->error('Укажите путь к файлу загрузки.');
        }

        if (file_exists($fileName) === false) {
            $io->error("Файл [$fileName] не существует.");
        }

        $count = $input->getArgument('count');

        $row = 1;
        $io->progressStart($count);
        if (($handle = fopen($fileName, "rb")) !== false) {
            while (($data = fgetcsv($handle, 1000)) !== false) {
                $io->progressAdvance();

                if (count($data) === 3) {
                    // Фамилия Имя
                    [$secondName, $firstName] = explode(' ', $data[0]);

                    // Возраст
                    $age = (int)$data[1];

                    // Город
                    $city = $data[2];

                    $this->authService->register(new UserRegisterDto(
                        $firstName,
                        $secondName,
                        $this->getBirthDate($age),
                        $city,
                        '',
                        random_bytes(15)
                    ));
                } else {
                    $io->error("В строке [$row] некорректные данные.");
                }

                $row++;
            }

            fclose($handle);
        }

        $io->progressFinish();

        return self::SUCCESS;
    }

    /**
     * @throws \Exception
     */
    private function getBirthDate(int $age): string
    {
        $currentYear = date('Y');

        $randomDate = random_int(
            mktime(0, 0, 0, 1, 1, $currentYear),
            mktime(0, 0, 0, 12, 31, $currentYear)
        );

        $currentDate = mktime(0, 0, 0, date('m'), date('d'), $currentYear);

        $day = date('d', $randomDate);
        $month = date('m', $randomDate);

        if ($randomDate <= $currentDate) {
            return date('Y-m-d', mktime(0, 0, 0, $month, $day, $currentYear - $age));
        }

        return date('Y-m-d', mktime(0, 0, 0, $month, $day, $currentYear - $age - 1));
    }
}
