function initClassicEditor(editorId, htmlViewId, visualBtnId, textBtnId) {
  let activeFormat = 'p';

  tinymce.init({
    selector: `#${editorId}`,
    height: 600,
    plugins: 'lists link image media table code',
    menubar: false,
    toolbar: 'customformat | bold italic underline | bullist numlist | link image media | undo redo | table | code',
    setup: function (editor) {
      const formats = [
        { text: 'Paragraph', block: 'p' },
        { text: 'Heading 1', block: 'h1' },
        { text: 'Heading 2', block: 'h2' },
        { text: 'Heading 3', block: 'h3' },
        { text: 'Heading 4', block: 'h4' },
        { text: 'Heading 5', block: 'h5' },
        { text: 'Heading 6', block: 'h6' },
      ];

      editor.ui.registry.addMenuButton('customformat', {
        text: 'Format',
        fetch: function (callback) {
          const currentNode = editor.selection.getNode();
          const currentTag = currentNode.nodeName.toLowerCase();

          const items = formats.map(fmt => ({
            type: 'menuitem',
            text: fmt.text,
            active: currentTag === fmt.block,
            onAction: () => {
              editor.execCommand('FormatBlock', false, fmt.block);
            }
          }));

          callback(items);
        }
      });

      editor.on('NodeChange', function () {
        const btn = editor.ui.registry.getAll().buttons.customformat;
        if (btn && btn.onSetup) btn.onSetup(editor);
      });
    },
    init_instance_callback: function (editor) {
      const visualBtn = document.getElementById(visualBtnId);
      const textBtn = document.getElementById(textBtnId);
      const htmlView = document.getElementById(htmlViewId);

      visualBtn.addEventListener('click', function () {
        if (!visualBtn.classList.contains('active')) {
          const htmlContent = htmlView.value;
          editor.setContent(htmlContent);
          editor.getContainer().style.display = '';
          htmlView.style.display = 'none';
          visualBtn.classList.add('active');
          textBtn.classList.remove('active');
        }
      });

      textBtn.addEventListener('click', function () {
        if (!textBtn.classList.contains('active')) {
          const content = editor.getContent();
          htmlView.value = content;
          editor.getContainer().style.display = 'none';
          htmlView.style.display = 'block';
          textBtn.classList.add('active');
          visualBtn.classList.remove('active');
        }
      });
    }
  });
}

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.wp-editor-container').forEach(function (container, index) {
      const editorId = `wp-editor-${index}`;
      const htmlId = `html-view-${index}`;
      const visualBtnId = `visual-btn-${index}`;
      const textBtnId = `text-btn-${index}`;

      // Set unique IDs dynamically
      container.querySelector('.wp-editor').setAttribute('id', editorId);
      container.querySelector('.html-view').setAttribute('id', htmlId);
      container.querySelector('.visualBtn').setAttribute('id', visualBtnId);
      container.querySelector('.textBtn').setAttribute('id', textBtnId);

      // Initialize the editor
      initClassicEditor(editorId, htmlId, visualBtnId, textBtnId);
    });
  });