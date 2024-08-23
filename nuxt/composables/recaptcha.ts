export default () => {
    const executeRecaptcha = (): Promise<string> => {
        return new Promise((resolve, reject) => {
            grecaptcha.ready(() => {
                grecaptcha.execute(useRuntimeConfig().public.recaptchaKey, { action: 'submit' }).then((token: string) => {
                    resolve(token);
                }).catch((error: any) => {
                    reject('Error executing reCAPTCHA: ' + error);
                });
            });
        });
    };
    return { executeRecaptcha };
}