<script>
    (() => {
        const savedTheme = localStorage.getItem('theme');
        const useDarkTheme = savedTheme
            ? savedTheme === 'dark'
            : window.matchMedia('(prefers-color-scheme: dark)').matches;

        document.documentElement.classList.toggle('dark', useDarkTheme);
    })();
</script>
