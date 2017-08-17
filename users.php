<?php include "template_parts/main_header.php"; ?>

<?php $result = get_users_list();

//var_dump('<pre>',$result);
?>
<div id="page-wrapper">
    <div class="graphs">
        <h3 class="blank1">Users</h3>
        <div class="xs tabls">
            <div class="bs-example4" data-example-id="contextual-table">
                <div id="jsGrid"></div>
            </div>

            </div>
        </div>
    </div>

    <script>
        $(function() {
            var obj = <?php echo json_encode($result); ?>;
            var array = Object.keys(obj).map(function (key) {
                return obj[key];
            });
            var newa = array.filter(function (el) {
                return el.user_id != "admin"
            });



            var db = {

                loadData: function(filter) {
                    return $.grep(this.clients, function(client) {
                        return (!filter.firstname || client.firstname.indexOf(filter.firstname) > -1)
                            && (!filter.lastname || client.lastname.indexOf(filter.lastname) > -1)
                            && (!filter.email || client.email.indexOf(filter.email) > -1);
                    });
                },

                insertItem: function(insertingClient) {
                    this.clients.push(insertingClient);
                },

                updateItem: function(updatingClient) { },

                deleteItem: function(deletingClient) {
                    var clientIndex = $.inArray(deletingClient, this.clients);
                    this.clients.splice(clientIndex, 1);
                }

            };

            window.db = db;



            db.clients = newa;

            $("#jsGrid").jsGrid({
                height: 300,
                width: "100%",

                filtering: true,
                editing: true,
                sorting: true,
                paging: true,
                autoload: true,

                pageSize: 2,
                pageButtonCount: 5,

                deleteConfirm: "Do you really want to delete the client?",

                controller: db,

                fields: [
                    { name: "firstname", type: "text", width: 80 },
                    { name: "lastname", type: "text", width: 80 },
                    { name: "email", type: "text", width: 80 },
                    { type: "control" }

                ]
            });
                        $("#sort").click(function() {
                var field = $("#sortingField").val();
                $("#jsGrid").jsGrid("sort", field);
            });
        });



        </script>
    <?php include "template_parts/main_footer.php"; ?>
