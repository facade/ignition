export default function editorUrl(config, file, lineNumber) {
    const editor = config.editor;
    const editors = {
        sublime: 'subl://open?url=file://%path&line=%line',
        textmate: 'txmt://open?url=file://%path&line=%line',
        emacs: 'emacs://open?url=file://%path&line=%line',
        macvim: 'mvim://open/?url=file://%path&line=%line',
        phpstorm: config.phpstormLink || 'http://localhost:63342/api/file?file=%path&line=%line',
        idea: 'idea://open?file=%path&line=%line',
        vscode: 'vscode://file/%path:%line',
        'vscode-insiders': 'vscode-insiders://file/%path:%line',
        'vscode-remote': 'vscode://vscode-remote/%path:%line',
        'vscode-insiders-remote': 'vscode-insiders://vscode-remote/%path:%line',
        vscodium: 'vscodium://file/%path:%line',
        atom: 'atom://core/open/file?filename=%path&line=%line',
        nova: 'nova://core/open/file?filename=%path&line=%line',
        netbeans: 'netbeans://open/?f=%path:%line',
        xdebug: 'xdebug://%path@%line',
    };

    file =
        (config.remoteSitesPath || false).length > 0 && (config.localSitesPath || false).length > 0
            ? file.replace(config.remoteSitesPath, config.localSitesPath)
            : file;

    if (!Object.keys(editors).includes(editor)) {
        console.error(
            `'${editor}' is not supported. Support editors are: ${Object.keys(editors).join(', ')}`,
        );

        return null;
    }

    let line = lineNumber;

    /** TODO check if lineNumber is always off by 1 in all platform, for example instead of line 1 it will open in line 2
     //*If that is the case uncomment the function below
     */
    // if (editor === 'phpstorm') { // or any JetBrain
    //     line = lineNumber - 1;
    // }

    if (!Number.isInteger(+line) || line < 0) {
        line = 0; // <---
    }
    return editors[editor]
        .replace('%path', encodeURIComponent(file))
        .replace('%line', encodeURIComponent(line));
}
