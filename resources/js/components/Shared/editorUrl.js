export default function editorUrl(editor, file, lineNumber) {
    const editors = {
        sublime: 'subl://open?url=file://%path&line=%line',
        textmate: 'txmt://open?url=file://%path&line=%line',
        emacs: 'emacs://open?url=file://%path&line=%line',
        macvim: 'mvim://open/?url=file://%path&line=%line',
        phpstorm: 'phpstorm://open?file=%path&line=%line',
        idea: 'idea://open?file=%path&line=%line',
        vscode: 'vscode://file/%path:%line',
        atom: 'atom://core/open/file?filename=%path&line=%line',
    };

    if (!Object.keys(editors).includes(editor)) {
        return null;
    }

    return editors[editor]
        .replace('%path', encodeURIComponent(file))
        .replace('%line', encodeURIComponent(lineNumber));
}
