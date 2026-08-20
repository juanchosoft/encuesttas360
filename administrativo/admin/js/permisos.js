$(document).on("ready", init);
var q, nombre, allFields, tips;
/**
 * se activa para inicializar el documento
 */
function init() {
    q = {};
    // No permitir cerrar el modal, click afuera
    $("#myModalPermisos").modal({ backdrop: "static", keyboard: false });
}

var PERMISOS = {
    editpermission: function(id) {
        q = {};
        q.op = "pms_usrpermission";
        q.id = id;
        $("#id").val(id);
        UTIL.callAjaxRqstPOST(q, this.editpermissionhandler);
    },
    editpermissionhandler: function(data) {
        UTIL.cursorNormal();
        if (data.output.valid) {
            var ava = data.output.available;
            var ass = data.output.assigned;

            // Función helper para obtener el tipo de permiso
            function getTipoPermiso(nombre) {
                if (nombre.includes('Ver')) return 'ver';
                if (nombre.includes('Crear')) return 'crear';
                if (nombre.includes('Editar')) return 'editar';
                if (nombre.includes('Permisos')) return 'permisos';
                return 'ver';
            }

            // Función helper para obtener el icono
            function getIcon(tipo) {
                switch(tipo) {
                    case 'ver': return 'fa-eye';
                    case 'crear': return 'fa-plus-circle';
                    case 'editar': return 'fa-edit';
                    case 'permisos': return 'fa-shield-alt';
                    default: return 'fa-check';
                }
            }

            // Función helper para obtener descripción
            function getDescripcion(tipo) {
                switch(tipo) {
                    case 'ver': return 'Visualizar información';
                    case 'crear': return 'Crear nuevos registros';
                    case 'editar': return 'Modificar registros';
                    case 'permisos': return 'Gestionar permisos';
                    default: return 'Permiso del sistema';
                }
            }

            var chks = "";
            for (var i in ava) {
                var tipo = getTipoPermiso(ava[i].nombre);
                var icon = getIcon(tipo);
                var descripcion = getDescripcion(tipo);

                chks += '<div class="permission-card">';
                chks += '  <div class="permission-toggle-container">';
                chks += '    <div class="permission-info">';
                chks += '      <div class="permission-icon ' + tipo + '">';
                chks += '        <i class="fas ' + icon + ' text-white"></i>';
                chks += '      </div>';
                chks += '      <div class="permission-details">';
                chks += '        <h6>' + ava[i].nombre;
                chks += '          <span class="permission-badge ' + tipo + '">#' + ava[i].id + '</span>';
                chks += '        </h6>';
                chks += '        <small>' + descripcion + '</small>';
                chks += '      </div>';
                chks += '    </div>';
                chks += '    <div class="form-check form-switch">';
                chks += '      <input class="form-check-input" type="checkbox" ';
                chks += '             value="' + ava[i].id + '" ';
                chks += '             name="chk' + ava[i].id + '" ';
                chks += '             id="chk' + ava[i].id + '">';
                chks += '    </div>';
                chks += '  </div>';
                chks += '</div>';
            }

            $("#permission").empty();
            $("#permission").append(chks);

            // Marcar los permisos ya asignados
            $("#formpermission :input").each(function() {
                var p = $(this).attr("id");
                for (var j in ass) {
                    var idchk = "chk" + ass[j].tbl_permiso_id;
                    if (p == idchk) {
                        $(this).prop("checked", true);
                    }
                }
            });

            $("#myModalPermisos").modal('show');

        } else {
            swal("warning", data.output.response.content, "error");
        }
    },
    getSeleccionarPermisos: function() {
        if ($("#check_permisos").is(":checked")) {
            $("#formpermission :input").each(function() {
                this.checked = true;
            });
        } else {
            $("#formpermission :input").each(function() {
                this.checked = false;
            });
        }
    },
    savepermission: function() {
        var chk = "";
        var inputs = document
            .getElementById("formpermission")
            .getElementsByTagName("input"); // get element by tag name
        for (var i in inputs) {
            if (inputs[i].type == "checkbox") {
                if ($("#" + inputs[i].id).is(":checked")) {
                    chk += $("#" + inputs[i].id).val() + "-";
                }
            }
        }
        q.op = "pms_usrsavepermission";
        q.chk = chk;
        UTIL.callAjaxRqstPOST(q, this.savepermissionhandler);
    },
    checkAll: function() {
        if ($("#check_permisos").is(":checked")) {
            $("#formpermission :input").each(function() {
                this.checked = true;
            });
        } else {
            $("#formpermission :input").each(function() {
                this.checked = false;
            });
        }
    },
    /**
     * Método para guardar los permisos
     */
    savepermission: function() {
        var chk = "";
        var inputs = document
            .getElementById("formpermission")
            .getElementsByTagName("input"); // get element by tag name
        for (var i in inputs) {
            if (inputs[i].type == "checkbox") {
                if ($("#" + inputs[i].id).is(":checked")) {
                    chk += $("#" + inputs[i].id).val() + "-";
                }
            }
        }
        q.op = "pms_usrsavepermission";
        q.chk = chk;
        UTIL.callAjaxRqstPOST(q, this.savepermissionhandler);
    },
    savepermissionhandler: function(data) {
        UTIL.cursorNormal();
        if (data.output.valid) {
            UTIL.mostrarMensajeExitoso('Permisos asignados correctamente');
            setTimeout(function() {
                window.location = return_page;
            }, 1000);
        } else {
            UTIL.mostrarMensajeError(data.output.response.content);
        }

    },
};