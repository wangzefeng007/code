if (window.Config.modulename == 'admin' && window.Config.controllername == 'index' && window.Config.actionname == "login") {
    require.config({
        paths: {
            'csmding': ['../addons/csmding/js/csmding'],
        },
        shim: {
        	csmding: ['css!../addons/csmding/css/csmding.css']
        },
    });
    require(['csmding'], function (csmding) {
    	csmding.mounted();
    });
}