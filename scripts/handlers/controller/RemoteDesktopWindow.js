//include scripts/handlers/core/WABEntity.js
if ('{remoteDesktopProtocol}'=="rdp") {
    window.open('{url}');
    getWindowManager().remove_window(entity.win.id);
};