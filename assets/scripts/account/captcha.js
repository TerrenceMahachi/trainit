function Captcha() { this.num1; this.num2; this.operator; }

    Captcha.prototype.Shuffle = function () {
        this.num1 = Math.floor((Math.random() * 9) + 1);
        this.num2 = Math.floor((Math.random() * 9) + 1);
        this.operator = Math.floor((Math.random() * 2) + 1);
        if ((this.num1 - this.num2) < 1) { this.Shuffle(); }
    }

    Captcha.prototype.Eval = function () {
        var result = "";
        switch (this.operator) {
            case 1: result = this.num1 + this.num2; break;
            case 2: result = this.num1 - this.num2; break;
        }
        return result;
    }

    Captcha.prototype.Equation = function () {
        switch (this.operator) {
            case 1: result = this.num1 + "+" + this.num2; break;
            case 2: result = this.num1 + "-" + this.num2; break;
        }
        return result + "=";
    }