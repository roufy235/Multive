<?php /** @noinspection UnknownInspectionInspection */


namespace MultiveLogger;


use Exception;
use MultiveLogger\models\UserModel;
use Psr\Log\LoggerInterface;

class LoggerNewAccount {

    private LoggerInterface $logger;

    public function __construct(LoggerFactory $logger) {
        $this->logger = $logger
            ->addFileHandler('user_creator.log')
            ->createLogger();
    }

    /**
     * @throws Exception
     * @noinspection PhpUnused
     */
    public function registerUser(UserModel $user): void {
        try {
            // Log success
            $this->logger->info(sprintf('User created: %s', $user->getUserId()));
        } catch (Exception $exception) {
            // Log error message
            $this->logger->error($exception->getMessage());
            throw $exception;
        }
    }

}
