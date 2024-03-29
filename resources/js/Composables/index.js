import {useDark, useToggle} from '@vueuse/core'
import {reactive} from 'vue'

export const isDark = useDark()
export const toggleDarkMode = useToggle(isDark)

if (localStorage.getItem('vueuse-color-scheme') === 'auto') {
    localStorage.setItem('vueuse-color-scheme', 'dark');
} else {
    localStorage.setItem('vueuse-color-scheme', 'dark');
}

export const sidebarState = reactive({
    isOpen: window.innerWidth > 1024,
    isHovered: false,
    handleHover(value) {
        if (window.innerWidth < 1024) {
            return
        }
        sidebarState.isHovered = value
    },
    handleWindowResize() {
        if (window.innerWidth <= 1024) {
            sidebarState.isOpen = false
        } else {
            sidebarState.isOpen = true
        }
    },
})

export const scrolling = reactive({
    down: false,
    up: false,
})

let lastScrollTop = 0

export const handleScroll = () => {
    let st = window.pageYOffset || document.documentElement.scrollTop
    if (st > lastScrollTop) {
        // downscroll
        scrolling.down = true
        scrolling.up = false
    } else {
        // upscroll
        scrolling.down = false
        scrolling.up = true
        if (st == 0) {
            //  reset
            scrolling.down = false
            scrolling.up = false
        }
    }
    lastScrollTop = st <= 0 ? 0 : st // For Mobile or negative scrolling
}

export function transactionFormat() {

    function formatDateTime(date, includeTime = true) {
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        const formattedDate = new Date(date);

        const day = formattedDate.getDate().toString().padStart(2, '0');
        const month = months[formattedDate.getMonth()];
        const year = formattedDate.getFullYear();
        const hours = formattedDate.getHours().toString().padStart(2, '0');
        const minutes = formattedDate.getMinutes().toString().padStart(2, '0');
        const seconds = formattedDate.getSeconds().toString().padStart(2, '0');

        if (includeTime) {
            return `${day} ${month} ${year} ${hours}:${minutes}:${seconds}`;
        } else {
            return `${day} ${month} ${year}`;
        }
    }

    function getStatusClass(status) {
        if (status === 'Successful') {
            return 'success';
        } else if (status === 'Submitted') {
            return 'warning';
        } else if (status === 'Processing') {
            return 'primary';
        } else if (status === 'Rejected') {
            return 'danger';
        } else {
            return ''; // Default case or handle other statuses
        }
    }

    function formatAmount(amount, decimalPlaces = 2) {
        const formattedAmount = parseFloat(amount).toFixed(decimalPlaces);
        const integerPart = formattedAmount.split('.')[0];
        const decimalPart = formattedAmount.split('.')[1];
        const integerWithCommas = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        return decimalPlaces > 0 ? `${integerWithCommas}.${decimalPart}` : integerWithCommas;
    }

    const formatType = (type) => {
        // Convert camelCase to snake_case
        const snakeCaseType = type.replace(/([a-z])([A-Z])/g, '$1_$2').toLowerCase();

        // Replace underscores with spaces and capitalize each word
        return snakeCaseType.replace(/_/g, ' ').replace(/\w\S*/g, (word) => {
            return word.charAt(0).toUpperCase() + word.substr(1).toLowerCase();
        });
    };

    const formatCategory = (category) => {
        // Replace underscores with a space
        const formattedType = category.replace(/_/g, ' ');

        // Split the string into words
        const words = formattedType.split(' ');

        // Capitalize the first word
        words[0] = words[0].charAt(0).toUpperCase() + words[0].slice(1);

        // Remove 's' from the last word if it's 'earnings'
        if (words[words.length - 1] === 'earnings') {
            words[words.length - 1] = 'Earning';
        }

        // Join the words back together
        return words.join(' ');
    };

    function formatDate(date) {
        const formattedDate = new Date(date).toLocaleDateString('en-CA', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            timeZone: 'Asia/Kuala_Lumpur'
        });

        const [year, month, day] = formattedDate.split('-');
        return `${day}/${month}/${year}`;
    }

    function formatTime(created_at) {
        // Convert timestamp to Date object
        const time = new Date(created_at);

        // Extract hours, minutes, and seconds
        const hours = time.getHours();
        const minutes = time.getMinutes();

        // Determine AM/PM
        const ampm = hours >= 12 ? 'PM' : 'AM';

        // Convert hours to 12-hour format
        const formattedHours = hours % 12 || 12;

        // Format the time in 12-hour format with AM/PM
        return `${formattedHours}:${minutes.toString().padStart(2, '0')} ${ampm}`;
    }

    function formatWalletAddress(address) {
        // Check if the address is valid and has at least 8 characters
        if (address && address.length >= 8) {
            const frontPart = address.slice(0, 4);
            const backPart = address.slice(-4);
            return `${frontPart}...${backPart}`;
        } else {
            return address; // Return the original address if it's invalid or too short
        }
    }

    return {
        formatDateTime,
        formatDate,
        formatTime,
        getStatusClass,
        formatAmount,
        formatType,
        formatCategory,
        formatWalletAddress,
    };
}
