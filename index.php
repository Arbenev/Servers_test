<?php
require './mysql.php';
$pdo = getConnection();
$query = 'SELECT * FROM `owners` ORDER BY `id`';
$config = parse_ini_file('config.ini', true);
$pageLength = $config['page']['length'];
?>
<!DOCTYPE html>
<html>
    <head>
        <title>ООО Сервер в Аренду. Тестовое задание</title>
        <script type="text/javascript" src="js/jquery.js"></script>
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <script>
            let pageLength = <?= $pageLength ?>;
            $(document).ready(function () {
                $('#owners').change(function () {
                    loadServers(1);
                });
            });

            function loadServers(page) {
                if (typeof (page) === 'undefined') {
                    page = 1;
                }

                let table = '<table id="servers"><thead><tr><th>N</th><th>Server ID</th><th>IP</th><th>IP description</th><th>Server name</th><th>Owner name</th></tr></thead><tbody></tbody></table>';
                $('#server_data').empty().append(table);
                let id = $('#owners').val();
                if (id !== '0') {
                    let request = JSON.stringify({
                        owner: id,
                        page: page
                    });
                    $.ajax(
                            '/servers/',
                            {
                                success: showServers,
                                processData: false,
                                type: 'POST',
                                data: request,
                                dataType: 'json'
                            }
                    );
                }
                return false;
            }
            function showServers(servers) {
                for (let i = 0; i < servers.owners.length; i++) {
                    let tr = '<tr>' +
                            '<td>' + (i + 1) + '</td>' +
                            '<td>' + servers.owners[i].id + '</td>' +
                            '<td>' + servers.owners[i].ip + '</td>' +
                            '<td>' + servers.owners[i].description + '</td>' +
                            '<td>' + servers.owners[i].server + '</td>' +
                            '<td>' + servers.owners[i].owner + '</td>' +
                            '</tr>';
                    $('#server_data table tbody').append(tr);
                }
                if (servers.count > 1) {
                    $('#server_data table tbody').after('<ul class="pagination"></ul>');
                    let pagination = $('#server_data table tbody').next('ul');
                    for (let i = 1; i < servers.page; i++) {
                        pagination.append('<li onclick="return loadServers(' + i + ');">' + i + '</li>');
                    }
                    let i = servers.page;
                    pagination.append('<li class="active">' + i + '</li>');
                    for (let i = (servers.page + 1); i <= servers.count; i++) {
                        pagination.append('<li onclick="return loadServers(' + i + ');">' + i + '</li>');
                    }
                }
            }
        </script>
    </head>
    <body>
        <h3>Clients</h3>
        <select id="owners">
            <option value="0">Please select an owner</option>
            <?php
            $st = m_pquery($pdo, $query);
            $owners = $st->fetchAll(PDO::FETCH_ASSOC);
            foreach ($owners as $owner) {
                ?>
                <option value="<?= $owner['id'] ?>"><?= $owner['name'] ?></option>
                <?php
            }
            ?>
        </select>
        <div id="server_data"></div>
    </body>
</html>
