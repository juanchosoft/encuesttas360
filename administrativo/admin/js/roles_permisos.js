$(document).on("ready", function () {
    $("#modalRol").modal({ backdrop: "static", keyboard: false });
});

var ROLES = {
    _catalogo: null, // module -> [{id, permission_key, module, action, name, description}]
    _editandoSistema: false,

    nuevo: function () {
        if (!ROLES_CAN_MANAGE) return;
        $("#formrol")[0].reset();
        $("#rol_id").val(0);
        ROLES._editandoSistema = false;
        ROLES._cargarCatalogo([]);
        $("#modalRol").modal("show");
    },

    editar: function (id) {
        if (!ROLES_CAN_MANAGE) return;
        UTIL.callAjaxRqstPOST({ op: "roleget", id: id }, ROLES._editarHandler);
    },

    _editarHandler: function (data) {
        UTIL.cursorNormal();
        if (!data.output || !data.output.valid) {
            UTIL.mostrarMensajeError("No se pudo cargar el rol.");
            return;
        }
        var r = data.output.response;
        $("#rol_id").val(r.id);
        $("#rol_name").val(r.name);
        $("#rol_description").val(r.description || "");

        ROLES._editandoSistema = (parseInt(r.is_system, 10) === 1);

        ROLES._cargarCatalogo(r.permission_ids || []);
        $("#modalRol").modal("show");
    },

    _cargarCatalogo: function (assignedIds) {
        UTIL.callAjaxRqstPOST({ op: "rolepermissionscatalog" }, function (data) {
            UTIL.cursorNormal();
            if (!data.output || !data.output.valid) {
                UTIL.mostrarMensajeError("No se pudo cargar el catálogo de permisos.");
                return;
            }
            ROLES._catalogo = data.output.response;
            ROLES._renderCatalogo(assignedIds);
        });
    },

    _renderCatalogo: function (assignedIds) {
        var assigned = {};
        for (var i = 0; i < assignedIds.length; i++) {
            assigned[assignedIds[i]] = true;
        }

        var html = "";
        for (var modulo in ROLES._catalogo) {
            if (!ROLES._catalogo.hasOwnProperty(modulo)) continue;
            var items = ROLES._catalogo[modulo];
            var moduloLabel = (items[0] && items[0].module_label) ? items[0].module_label : modulo;
            html += '<div class="module-group">';
            html += '  <div class="module-group-header">' + moduloLabel + "</div>";
            html += '  <div class="module-group-body">';
            for (var j = 0; j < items.length; j++) {
                var it = items[j];
                var checked = assigned[it.id] ? "checked" : "";
                html += '<div class="form-check">';
                html += '  <input class="form-check-input rol-perm-chk" type="checkbox" value="' + it.id + '" id="rolperm_' + it.id + '" ' + checked + ">";
                html += '  <label class="form-check-label perm-check-label" for="rolperm_' + it.id + '">' + it.name;
                // Clave técnica oculta (quitar "d-none" para mostrarla).
                html += '    <span class="perm-check-key d-none">' + it.permission_key + "</span>";
                html += "  </label>";
                html += "</div>";
            }
            html += "  </div>";
            html += "</div>";
        }
        $("#rol_permisos_container").html(html);
        $("#rol_check_all").prop("checked", false);
    },

    toggleAll: function (checked) {
        $(".rol-perm-chk").prop("checked", checked);
    },

    guardar: function () {
        if (!ROLES_CAN_MANAGE) return;

        var name = $("#rol_name").val().trim();
        if (!name) {
            UTIL.mostrarMensajeError("El nombre del rol es obligatorio.");
            return;
        }

        var chk = "";
        $(".rol-perm-chk:checked").each(function () {
            chk += $(this).val() + "-";
        });

        var q = {
            op: "rolesave",
            id: $("#rol_id").val(),
            name: name,
            // role_key ya no se envía: se genera automáticamente en el
            // servidor a partir del nombre solo al crear el rol.
            description: $("#rol_description").val().trim(),
            chk: chk,
        };

        UTIL.callAjaxRqstPOST(q, function (data) {
            UTIL.cursorNormal();
            if (data.output && data.output.valid) {
                UTIL.mostrarMensajeExitoso("Rol guardado correctamente.");
                setTimeout(function () {
                    window.location.reload();
                }, 800);
            } else {
                var msg = (data.output && data.output.response && data.output.response.content) || "No fue posible guardar el rol.";
                UTIL.mostrarMensajeError(msg);
            }
        });
    },

    eliminar: function (id, nombre) {
        if (!ROLES_CAN_MANAGE) return;
        if (!confirm('¿Eliminar el rol "' + nombre + '"? Esta acción no se puede deshacer.')) return;

        UTIL.callAjaxRqstPOST({ op: "roledelete", id: id }, function (data) {
            UTIL.cursorNormal();
            if (data.output && data.output.valid) {
                UTIL.mostrarMensajeExitoso("Rol eliminado.");
                setTimeout(function () {
                    window.location.reload();
                }, 800);
            } else {
                var msg = (data.output && data.output.response && data.output.response.content) || "No fue posible eliminar el rol.";
                UTIL.mostrarMensajeError(msg);
            }
        });
    },
};
