@extends("system.layout.master")
@section("title","Home")
@section("content")

<table width="100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td width="200px"></td>
        <td align="center">
            <p>&nbsp;</p>
            <table width="550px" border="0" cellspacing="0" cellpadding="10" style="border:1px solid #CAD2A7;background-color: #FFF">
                <tr>
                    <td colspan="3" align="center" class="form-header">System Admin Modules</td>
                </tr>
                <?php
                $homeModules = [ ];
                foreach ( Config::get("module") as $module ) {
                    if ( $module["showInHome"] ) {
                        $homeModules[] = $module;
                    }
                }
                $columnsCount = 3;
                $rowsCount = count($homeModules) / $columnsCount;
                if ( count($homeModules) % $columnsCount != 0 ) {
                    $rowsCount ++;
                }
                for ( $rowIdx = 0; $rowIdx < $rowsCount; $rowIdx++ ) {
                    //Print: icon line
                    ?>
                    <tr>
                        <?php
                        for ( $columnIdx = 0; $columnIdx < $columnsCount; $columnIdx++ ) {
                            $idx = $rowIdx * $columnsCount + $columnIdx;
                            if ( $idx < count($homeModules) ) {
                                $module = $homeModules[$idx];
                                ?>
                                <td align="center">
                                    <img src="<?= $module["homeIcon"] ?>" style="cursor: pointer;width: 64px" 
                                         onclick="window.location.href = '<?= $module["url"] ?>'"/>
                                </td>
                                <?php
                            } else {
                                ?>
                                <td></td>
                                <?php
                            }
                        }
                        ?>
                    </tr>
                    <?php
                    //Print: label line
                    ?>
                    <tr>
                        <?php
                        for ( $columnIdx = 0; $columnIdx < $columnsCount; $columnIdx++ ) {
                            $idx = $rowIdx * $columnsCount + $columnIdx;
                            if ( $idx < count($homeModules) ) {
                                $module = $homeModules[$idx];
                                ?>
                                <td  align="center"><a href="<?= $module["url"] ?>" class="command-link"><?= $module["title"] ?></a></td>
                                <?php
                            } else {
                                ?>
                                <td></td>
                                <?php
                            }
                        }
                        ?>
                    </tr>
                    <?php
                }
                ?>
                <tr>
                    <td colspan="3" align="center" style="font-family:Arial;font-size:12px;background-color:#CAD2A7;padding:3px">
                        Build <?= Config::get("sa.version") ?>
                    </td>
                </tr>
            </table>
            <p>&nbsp;</p>
            <p>&nbsp;</p>

        </td>
        <td width="200px" valign="top">
        </td>
    </tr>
</table>
@stop