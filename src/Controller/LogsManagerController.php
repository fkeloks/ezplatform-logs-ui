<?php

namespace EzPlatformLogsUi\Bundle\Controller;

use EzPlatformLogsUi\Bundle\LogManager\LogFile;
use EzPlatformLogsUi\Bundle\LogManager\LogTrunkCache;
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
        $projectDir = $this->getParameterSafely('kernel.project_dir');

        if ($logPath !== null && $projectDir !== null) {
            $formattedLogPath = str_replace([$projectDir . '\\', DIRECTORY_SEPARATOR], ['', '/'], $logPath);
        }

        if ($logPath === null || !file_exists($logPath)) {
            return $this->render('EzPlatformLogsUiBundle:logs:index.html.twig', [
                'logPath'        => $formattedLogPath ?? $logPath,
                'currentChunkId' => $chunkId,
                'total'          => null,
                'logs'           => []
            ]);
        }

        $logFile = new LogFile($logPath);
        $logTrunkCache = new LogTrunkCache($logPath, $cacheDir, 'ezplatform_logs_ui');

        if ($chunkId >= 2) {
            $total = $logTrunkCache->getCacheSystem()->get($logTrunkCache->getCacheKey('total'), 0);
            if ($chunkId > ($total / 20)) {
                $chunkId = 1;
            }
        }

        if (!$logTrunkCache->hasChunk($chunkId)) {
            $lines = $logFile->tail(1000);

            if (!empty($lines)) {
                $total = count($lines);
                $logTrunkCache->getCacheSystem()->set($logTrunkCache->getCacheKey('total'), $total);

                foreach (array_chunk($lines, 20) as $index => $chunk) {
                    $logTrunkCache->setChunk($index + 1, $chunk);
                }

                $logs = array_slice($logFile->parse($lines), 0, 20);
            }
        } else {
            $total = $logTrunkCache->getCacheSystem()->get($logTrunkCache->getCacheKey('total'));
            $lines = $logTrunkCache->getChunk($chunkId);
            $logs = $logFile->parse($lines);
        }

        return $this->render('EzPlatformLogsUiBundle:logs:index.html.twig', [
            'logPath'        => $formattedLogPath ?? $logPath,
            'currentChunkId' => $chunkId,
            'total'          => $total ?? 0,
            'logs'           => $logs ?? []
        ]);
    }

    /**
     * @return Response
     *
     * @throws InvalidArgumentException
     */
    public function reloadAction(): Response {
        $logPath = $this->getParameterSafely('log_path');
        $cacheDir = $this->getParameterSafely('kernel.cache_dir');

        if ($logPath !== null && file_exists($logPath)) {
            $logFile = new LogFile($logPath);
            $logTrunkCache = new LogTrunkCache($logPath, $cacheDir, 'ezplatform_logs_ui');

            $lines = $logFile->tail(1000);

            if (!empty($lines)) {
                $oldTotal = $logTrunkCache->getCacheSystem()->get($logTrunkCache->getCacheKey('total'), 0);
                if ($oldTotal) {
                    $logTrunkCache->clearChunks($oldTotal);
                }

                $total = count($lines);
                $logTrunkCache->getCacheSystem()->set($logTrunkCache->getCacheKey('total'), $total);

                foreach (array_chunk($lines, 20) as $index => $chunk) {
                    $logTrunkCache->setChunk($index + 1, $chunk);
                }
            }
        }

        return $this->redirectToRoute('ezplatform_logs_ui_index');
    }

}
