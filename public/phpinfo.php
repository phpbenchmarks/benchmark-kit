<style type="text/css">
    span.warning {
        background-color: #dc9348;
        padding: 3px 5px;
    }
</style>
<div class="center">
    <table>
        <tbody>
            <tr class="h" style="background-color: #44a6d3">
                <td colspan="2">
                    <h1 class="p">pbpenchmarks.com benchmark kit</h1>
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
        </tbody>
    </table>
</div>

<?php
phpinfo();
