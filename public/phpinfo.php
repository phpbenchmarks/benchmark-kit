<?php
require(__DIR__ . '/../src/Utils/Path.php');

$opcacheStatus = opcache_get_status();
?>
<style type="text/css">
    body {
        cursor: default;
    }
    span.warning {
        background-color: #dc9348;
        padding: 3px 5px;
    }
    tr.h_phpbenchmarks {
        background-color: #44a6d3;
    }
</style>
<div class="center">
    <table>
        <tbody>
            <tr class="h h_phpbenchmarks">
                <td colspan="2">
                    <h1 class="p">OPcache configuration</h1>
                </td>
            </tr>
            <tr>
                <td class="e">opcache.enable</td>
                <td class="v">
                    <?php if (ini_get('opcache.enable') === '1') { ?>
                        On
                    <?php } else { ?>
                        <span class="warning">Off</span> (<strong>phpbenchkit benchmark:init</strong> to enable it)
                    <?php } ?>
                </td>
            </tr>
            <tr>
                <td class="e">opcache.max_accelerated_files</td>
                <td class="v"><?= number_format(ini_get('opcache.max_accelerated_files')) ?></td>
            </tr>
            <tr>
                <td class="e">opcache.max_file_size</td>
                <td class="v"><?= number_format(ini_get('opcache.max_file_size')) ?></td>
            </tr>
            <tr>
                <td class="e">opcache.revalidate_freq</td>
                <td class="v"><?= number_format(ini_get('opcache.revalidate_freq')) ?></td>
            </tr>
            <tr>
                <td class="e">opcache.save_comments</td>
                <td class="v"><?= ini_get('opcache.save_comments') ?></td>
            </tr>
        </tbody>
    </table>

    <table>
        <tbody>
            <tr class="h h_phpbenchmarks">
                <td colspan="3">
                    <h1 class="p">opcache_get_status()</h1>
                </td>
            </tr>
            <?php $currentStatusKey = null ?>
            <?php foreach (['memory_usage', 'interned_strings_usage', 'opcache_statistics'] as $statusKey) { ?>
                <?php foreach ($opcacheStatus[$statusKey] as $key => $value) { ?>
                    <tr class="<?php if ($currentStatusKey === $statusKey) echo 'foo' ?>">
                        <?php if ($currentStatusKey !== $statusKey) { ?>
                            <td class="e" rowspan="<?= count($opcacheStatus[$statusKey]) ?>>"><?= $statusKey ?></td>
                        <?php } ?>
                        <td class="v"><?= $key ?></td>
                        <td class="v">
                            <?php
                            if (in_array($key, ['start_time', 'last_restart_time'])) {
                                echo $value === 0 ? $value : (new \DateTime())->setTimestamp($value)->format('Y-m-d H:i:s');
                            } elseif (is_numeric($value)) {
                                echo number_format($value);
                            } else {
                                echo $value;
                            }
                            ?>
                        </td>
                    </tr>
                    <?php $currentStatusKey = $statusKey ?>
                <?php } ?>
            <?php } ?>
        </tbody>
    </table>

    <?php $preload = ini_get('opcache.preload'); ?>
    <?php if (is_string($preload)) { ?>
        <table>
            <tbody>
                <tr class="h h_phpbenchmarks">
                    <td colspan="2">
                        <h1 class="p">OpCache preload configuration</h1>
                    </td>
                </tr>
                <tr>
                    <td class="e">opcache.preload</td>
                    <td class="v">
                        <?php if (strlen(ini_get('opcache.preload'))) { ?>
                            <?= App\Utils\Path::rmPrefix($preload) ?>
                        <?php } else { ?>
                            <span class="warning">disabled</span> (<strong>phpbenchkit benchmark:init</strong> to enable it)
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <td class="e">opcache.preload_user</td>
                    <td class="v"><?= ini_get('opcache.preload_user') ?></td>
                </tr>
            </tbody>
        </table>

        <table>
            <tbody>
                <tr class="h h_phpbenchmarks">
                    <td colspan="2">
                        <h1 class="p">opcache_get_status()['preload_statistics']</h1>
                    </td>
                </tr>
                <tr>
                    <td class="e">memory_consumption</td>
                    <td class="v"><?= number_format($opcacheStatus['preload_statistics']['memory_consumption']) ?></td>
                </tr>
                <tr>
                    <td class="e" rowspan="<?= max(1, count($opcacheStatus['preload_statistics']['scripts'])) ?>">scripts</td>
                    <td class="v">
                        <?= count($opcacheStatus['preload_statistics']['scripts']) > 0 ? App\Utils\Path::rmPrefix($opcacheStatus['preload_statistics']['scripts'][0]) : null ?>
                    </td>
                </tr>
                <?php foreach ($opcacheStatus['preload_statistics']['scripts'] as $index => $script) { ?>
                    <?php if ($index > 0) { ?>
                        <tr>
                            <td class="v"><?= App\Utils\Path::rmPrefix($script) ?></td>
                        </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    <?php } ?>
</div>

<?php
phpinfo();
