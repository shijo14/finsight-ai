<script>
(function () {
    var key = 'finsight-theme';
    var stored = localStorage.getItem(key);
    var theme = stored || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    if (theme === 'dark') {
        document.documentElement.classList.add('dark');
    }
    document.documentElement.style.colorScheme = theme;
})();
</script>
