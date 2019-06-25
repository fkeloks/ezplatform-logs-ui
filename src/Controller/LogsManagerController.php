<?php

namespace EzPlatformLogsUi\Bundle\Controller;

use EzPlatformLogsUi\Bundle\Filesystem\LogFile;
use EzPlatformLogsUi\Bundle\Filesystem\LogTrunkCache;
use Psr\SimpleCache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class LogsManagerController
 *
 * @author Florian Bouché <contact@florian-bouche.fr>
 *
 * @package EzPlatformLogsUi\Bundle\Controller
 */
class LogsManagerController extends Controller {

    /**
     * @param string $name
     *
     * @return mixed|null
     */
    protected function getParameterSafely(string $name) {
        try {
            return $this->container->getParameter($name);
        } catch (\InvalidArgumentException $argumentException) {
            return null;
        }
    }

    /**
     * @param int $chunkId
     *
     * @return Response
     *
     * @throws InvalidArgumentException
     */
    public function indexAction(int $chunkId = 1): Response {
        $logPath = $this->getParameterSafely('log_path');
        $cacheDir = $this->getParameterSafely('kernel.cache_dir');

        if (!file_exists($logPath)) {
            throw new \RuntimeException('No log file found in ' . $logPath . '.');
        }

        $logFile = new LogFile($logPath);
        $logTrunkCache = new LogTrunkCache($logPath, $cacheDir, 'ezplatform_logs_ui');

        if (!$logTrunkCache->hasChunk($chunkId)) {
            if ($chunkId >= 2) {
                $total = $logTrunkCache->getCacheSystem()->get($logTrunkCache->getCacheKey('total'), 0);
                if ($chunkId > ($total / 20)) {
                    $chunkId = (int) ceil($total / 20);
                }
            }

            $lines = $logFile->read();

            if (!empty($lines)) {
                $total = count($lines);
                $logTrunkCache->getCacheSystem()->set($logTrunkCache->getCacheKey('total'), $total);

                foreach (array_chunk($lines, 20) as $index => $chunk) {
                    $logTrunkCache->setChunk($index + 1, $chunk);
                }

                $logs = array_reverse(array_slice($logFile->parse($lines), -20));
            }
        } else {
            $total = $logTrunkCache->getCacheSystem()->get($logTrunkCache->getCacheKey('total'));
            $lines = $logTrunkCache->getLastChunk($chunkId, $total);
            $logs = array_reverse($logFile->parse($lines));
        }

        return $this->render('EzPlatformLogsUiBundle:logs:index.html.twig', [
            'currentChunkId' => $chunkId,
            'total'          => $total ?? 0,
            'logs'           => $logs ?? []
        ]);
    }

}
