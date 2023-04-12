<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Fruit;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;


class FruitsFetchCommand extends Command
{
    protected static $defaultName = 'fruits:fetch';

    private $entityManager;

    public function __construct(MailerInterface $mailer, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Fetches all fruits from https://fruityvice.com/ and saves them to the database');
    }

    /**
     * {@inheritdoc}
     *
     * @return int
     */

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $httpClient = HttpClient::create();
        $response = $httpClient->request('GET', 'https://fruityvice.com/api/fruit/all');

        $fruitsData = json_decode($response->getContent(), true);

        foreach ($fruitsData as $fruitData) {
            $fruit = new Fruit();
            $fruit->setName($fruitData['name']);
            $fruit->setFruitId($fruitData['id']);
            $fruit->setFamily($fruitData['family']);
            $fruit->setFruitOrder($fruitData['order']);
            $fruit->setGenus($fruitData['genus']);
            $fruit->setNutritions($fruitData['nutritions']);

            $this->entityManager->persist($fruit);
        }

        $this->entityManager->flush();

        $output->writeln('Data Inserted!');

        // Send email notification
        $email = (new Email())
            ->from('harpreet.developer.02@gmail.com')
            ->to('harpreet.developer.02@gmail.com')
            ->subject('Fruits Fetch Command completed')
            ->text('All fruits have been fetched and saved to the database.');

            $this->mailer->send($email);

            $output->writeln('Email sent!');

            return Command::SUCCESS;
    }
}
