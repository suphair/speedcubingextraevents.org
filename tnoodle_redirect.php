
<?php
$data_tnoodle = $_GET['data'];
$filename = $_GET['filename'];
$allowed = $_GET['allowed'];
?>
<script src="//<?= $_SERVER['HTTP_HOST'] . str_replace("tnoodle_redirect.php/", "", $_SERVER['PHP_SELF']) ?>jQuery/jquery-3.4.1.min.js" type="text/javascript"></script>
<script>

    function getVersion(data, key_version) {
        $.each(data, function (key, val) {
            if (key === key_version) {
                $('#version').html(val);
            }
        });
    }

    function check_tnoodle() {
        $.getJSON('http://localhost:2014/version', function (data) {
            getVersion(data, 'runningVersion');
            version_get_done();
        })
                .fail(function () {
                    $.getJSON('http://localhost:2014/version.json', function (data) {
                        getVersion(data, 'running_version');
                        version_get_done();
                    }).fail(function () {
                        $('#status').html('Error!<br> Tnoodle not runnig');
                    });
                });
    }

    check_tnoodle();

</script>
<p>
    <span id="version" style="display: none"></span>
    <span id="status">Wait for the Tnoodle check...</span>
</p>
<script>
    function version_get_done() {
        var allowed = false;
        var current = $('#version').html();

        $.map($.parseJSON('<?= $allowed ?>'), function (value, key) {
            if (current === value) {
                allowed = true;
            }
        });

        if (allowed) {
            downoad();
        } else {
            $('#status').html('Error! <br>Allowed versions: <?= implode(",", json_decode($allowed)) ?> <br>Runnig version: ' + current);
        }
    }
    ;
</script>
<br>
<script>
    var filename = "<?= $filename ?>";
    var data = '<?= $data_tnoodle ?>';

    function downoad() {
        $('#status').html('loading...');
        xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            var a;
            if (xhttp.readyState === 4 && xhttp.status === 200) {
                a = document.createElement('a');
                a.href = window.URL.createObjectURL(xhttp.response);
                a.download = "<?= $filename ?>.zip";
                a.style.display = 'none';
                document.body.appendChild(a);
                a.click();
                $('#status').html('[' + filename + ']<br>download completed');
                b = document.createElement('a');
                b.innerHTML = 'close';
                b.href = '#';
                b.addEventListener("click", function () {
                    window.close();
                });
                document.body.appendChild(b);
            }
            if (xhttp.status === 400 || xhttp.status === 404) {
                $('#status').html(xhttp.statusText);
            }
        };
        xhttp.open("POST", 'http://localhost:2014/wcif/zip');
        xhttp.setRequestHeader("Content-Type", "application/json");
        xhttp.responseType = 'blob';
        xhttp.send(data);
    }
    ;
</script>