function Oauth2(data) {
    if (arguments.callee._singletonInstance) {
    return arguments.callee._singletonInstance;
  }

  arguments.callee._singletonInstance = this;
    
    this.accessToken = null;
    this.accessTokenExpiry = null;
    
    this.refreshToken = null;
    this.refreshTokenExpiry = null;
    
    this.clientId = null;
    this.clientSecret = null;
    this.clientSecretToken = null;
    
    this.username = null;
    this.password = null;
    
    this.siteBaseUrl = '/';
    if(data) {
        if(data.username) {
            this.username = data.username;
        }

        if(data.password) {
            this.password = data.password;
        }

        if(data.clientId) {
            this.clientId = data.clientId;
        }

        if(data.clientSecret) {
            this.clientSecret = data.clientSecret;
        }
        
        if(data.siteBaseUrl) {
            this.siteBaseUrl = data.siteBaseUrl;
        }
    }
    this.checkAjaxAccessToken = function ( ) {
        var date = new Date();
        if (
                this.accessToken === null
                || this.accessTokenExpiry === null
                || this.accessTokenExpiry.getTime() < date.getTime()
                ) {
            return false;
        }
        return true;
    };

    this.getAjaxAccessToken = function ( ) {
        if (!this.checkAjaxAccessToken()) {
            return this.requestAccessToken();
        }
        return this.accessToken;
    };

    this.requestAccessToken = function () {
        var accesstokenData = null;
        $.ajax({
            url: this.siteBaseUrl + "api/oauth2/token",
            method: 'post',
            async: false,
            data: {"grant_type": "bearer"},
            dataType: 'json',
            headers: {
                'authorization': 'Bearer ' + this.getAjaxRefreshToken()
            },
            error: function () {
                this.requestAjaxRefreshToken();
                accesstokenData = this.requestAccessToken();
                return null;
            },
            success: function (data, status) {
                accesstokenData = data;
                return data;
            }
        });
        this.accessToken = accesstokenData.token;
        this.accessTokenExpiry = new Date();
        this.accessTokenExpiry.setTime( this.accessTokenExpiry.getTime() + ( accesstokenData.ttl * 1000 ) );
        return accesstokenData.token;
    };

    this.checkAjaxRefreshToken = function () {
        var date = new Date();
        if (
                this.refreshToken === null
                || this.refreshTokenExpiry === null
                || this.refreshTokenExpiry.getTime() < date.getTime()
                ) {
            return false;
        }
        return true;
    };

    this.getAjaxRefreshToken = function () {
        if (!this.checkAjaxRefreshToken()) {
            return this.requestAjaxRefreshToken();
        }
        return this.refreshToken;
    };

    this.requestAjaxRefreshToken = function() {
        var refreshtokenData = null;
        $.ajax({
            url: this.siteBaseUrl + "api/oauth2/refreshtoken",
            method: 'post',
            async: false,
            data: {"grant_type": "password"},
            dataType: 'json',
            headers: {
                'authorization': "Basic " + btoa(this.getAjaxClientId() + ":" + this.getAjaxClientSecret()),//'Bearer ' + this.getAjaxRefreshToken()
            },
            error: function () {
                this.requestAjaxClientId();
                this.requestAjaxClientSecret();
                refreshtokenData = this.requestRefreshToken();
                return null;
            },
            success: function (data, status) {
                refreshtokenData = data;
                return data;
            }
        });
        this.refreshToken = refreshtokenData.token;
        this.refreshTokenExpiry = new Date();
        this.refreshTokenExpiry.setTime( this.refreshTokenExpiry.getTime() + ( refreshtokenData.ttl * 1000 ) );
        return refreshtokenData.token;
    };

    this.getAjaxClientId = function ( username, password ) {
        if(this.clientId==null)
            this.requestAjaxClientId( username, password );
        return this.clientId;
    };

    this.requestAjaxClientId = function (username, password) {
        if(!username) {
            username = this.username;
        }
        
        if(!password) {
            password = this.password;
        }
        
        
        var clientIdData = null;
        if(username && password) {
            $.ajax({
                url: this.siteBaseUrl + "api/oauth2/clientid",
                method: 'post',
                async: false,
                data: {"grant_type": "password"},
                dataType: 'json',
                headers: {
                    'authorization': "Basic " + btoa(username + ":" + password),//'Bearer ' + this.getAjaxRefreshToken()
                },
                error: function () {
                    clientIdData = null;
                    return null;
                },
                success: function (data, status) {
                    clientIdData = data;
                    return data;
                }
            });
        }
        else {
            $.ajax({
                url: this.siteBaseUrl + "api/oauth2/clientid",
                method: 'post',
                async: false,
                dataType: 'json',
                error: function () {
                    clientIdData = null;
                    return null;
                },
                success: function (data, status) {
                    clientIdData = data;
                    return data;
                }
            });
        }
        if(!clientIdData) {
            return null;
        }
        this.clientSecretToken = clientIdData.token;
        this.clientId = clientIdData.client_id;
        return clientIdData;
    };

    
    this.getAjaxClientSecret = function () {
        if(this.clientSecret===null) {
            this.requestAjaxClientSecret();
        }
        return this.clientSecret;
    };
    
    this.requestAjaxClientSecret = function ( token ) {
        
        if(!token) {
            if(this.clientSecretToken) {
                token = this.clientSecretToken;
                this.clientSecretToken = null;
            }
            else {
                var clientIdData = this.requestAjaxClientId();
                token = clientIdData.token;
            }
        }
        
        var clientSecretData = null;
        $.ajax({
            url: this.siteBaseUrl + "api/oauth2/clientsecret",
            method: 'post',
            async: false,
            data: {"grant_type": "bearer"},
            dataType: 'json',
            headers: {
                'authorization': 'Bearer ' + token
            },
            error: function () {
                this.requestAjaxRefreshToken();
                clientSecretData = this.requestAccessToken();
                return null;
            },
            success: function (data, status) {
                clientSecretData = data;
                return data;
            }
        });
        
        this.clientSecret = clientSecretData.client_secret;
        return clientSecretData;
    };

    this.requestToken = function (url, token) {

    };
}