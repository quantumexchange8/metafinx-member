// Extra icons

import { defineComponent } from 'vue'

export const MenuFoldLineRightIcon = defineComponent({
    setup() {
        return () => (
            <svg
                viewBox="0 0 24 24"
                stroke="currentColor"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path
                    d="M12 6H20M12 12H20M4 18H20M4 6L8 9L4 12"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                />
            </svg>
        )
    },
})

export const MenuFoldLineLeftIcon = defineComponent({
    setup() {
        return () => (
            <svg
                viewBox="0 0 24 24"
                stroke="currentColor"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path
                    d="M12 6H20M12 12H20M4 18H20M8 6L4 9L8 12"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                />
            </svg>
        )
    },
})

export const DashboardIcon = defineComponent({
    setup() {
        return () => (
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g clip-path="url(#clip0_338_5982)">
                    <path d="M8.71771 0H1.82723C0.819634 0 0 0.819634 0 1.82723V8.71771C0 9.7253 0.819634 10.5449 1.82723 10.5449H8.71771C9.7253 10.5449 10.5449 9.7253 10.5449 8.71771V1.82723C10.5448 0.819634 9.7253 0 8.71771 0Z" fill="#FF2D55"/>
                    <path d="M22.1728 0H15.2823C14.2747 0 13.4551 0.819634 13.4551 1.82723V8.71771C13.4551 9.7253 14.2747 10.5449 15.2823 10.5449H22.1728C23.1804 10.5449 24 9.7253 24 8.71771V1.82723C24 0.819634 23.1804 0 22.1728 0Z" fill="#D2D6DB"/>
                    <path d="M8.71771 13.4551H1.82723C0.819634 13.4551 0 14.2746 0 15.2822V22.1727C0 23.1803 0.819634 23.9999 1.82723 23.9999H8.71771C9.7253 23.9999 10.5449 23.1803 10.5449 22.1727V15.2822C10.5448 14.2746 9.7253 13.4551 8.71771 13.4551Z" fill="#D2D6DB"/>
                    <path d="M22.1728 13.4551H15.2823C14.2747 13.4551 13.4551 14.2747 13.4551 15.2823V22.1728C13.4551 23.1804 14.2747 24 15.2823 24H22.1728C23.1804 23.9999 24 23.1803 24 22.1727V15.2822C24 14.2746 23.1804 13.4551 22.1728 13.4551Z" fill="#FF2D55"/>
                </g>
                <defs>
                    <clipPath id="clip0_338_5982">
                        <rect width="24" height="24" fill="white"/>
                    </clipPath>
                </defs>
            </svg>
        )
    },
})

export const ArrowsInnerIcon = defineComponent({
    setup() {
        return () => (
            <svg
                viewBox="0 0 24 24"
                stroke="currentColor"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path
                    d="M4 4L9 9M9 9V5M9 9H5M20 4L15 9M15 9V5M15 9H19M4 20L9 15M9 15H5M9 15V19M20 20L15 15M15 15H19M15 15V19"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                />
            </svg>
        )
    },
})

export const EmptyCircleIcon = defineComponent({
    setup() {
        return () => (
            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path
                    d="M20.3149 15.4442C20.7672 14.3522 21 13.1819 21 12C21 9.61305 20.0518 7.32387 18.364 5.63604C16.6761 3.94821 14.3869 3 12 3C9.61305 3 7.32387 3.94821 5.63604 5.63604C3.94821 7.32387 3 9.61305 3 12C3 13.1819 3.23279 14.3522 3.68508 15.4442C4.13738 16.5361 4.80031 17.5282 5.63604 18.364C6.47177 19.1997 7.46392 19.8626 8.55585 20.3149C9.64778 20.7672 10.8181 21 12 21C13.1819 21 14.3522 20.7672 15.4442 20.3149C16.5361 19.8626 17.5282 19.1997 18.364 18.364C19.1997 17.5282 19.8626 16.5361 20.3149 15.4442Z"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                />
            </svg>
        )
    },
})

export const UserIcon = defineComponent({
    setup() {
        return () => (
            <svg class="text-gray-400"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round">  <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />  <circle cx="12" cy="7" r="4" /></svg>
        )
    },
})

export const UsersIcon = defineComponent({
    setup() {
        return () => (
            <svg class="text-gray-400"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round">  <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />  <circle cx="9" cy="7" r="4" />  <path d="M23 21v-2a4 4 0 0 0-3-3.87" />  <path d="M16 3.13a4 4 0 0 1 0 7.75" /></svg>
        )
    },
})

export const BellIcon = defineComponent({
    setup() {
        return () => (
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M9.35419 21C10.0593 21.6224 10.9856 22 12 22C13.0145 22 13.9407 21.6224 14.6458 21M18 8C18 6.4087 17.3679 4.88258 16.2427 3.75736C15.1174 2.63214 13.5913 2 12 2C10.4087 2 8.8826 2.63214 7.75738 3.75736C6.63216 4.88258 6.00002 6.4087 6.00002 8C6.00002 11.0902 5.22049 13.206 4.34968 14.6054C3.61515 15.7859 3.24788 16.3761 3.26134 16.5408C3.27626 16.7231 3.31488 16.7926 3.46179 16.9016C3.59448 17 4.19261 17 5.38887 17H18.6112C19.8074 17 20.4056 17 20.5382 16.9016C20.6852 16.7926 20.7238 16.7231 20.7387 16.5408C20.7522 16.3761 20.3849 15.7859 19.6504 14.6054C18.7795 13.206 18 11.0902 18 8Z" stroke="#9DA4AE" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        )
    },
})

export const LogoutIcon = defineComponent({
    setup() {
        return () => (
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g clip-path="url(#clip0_625_359)">
                    <path d="M15.4359 3.83906L22.7484 0C23.3868 0.239016 24 1.02234 24 1.92656V18.3187C24 19.0359 23.6484 19.6828 23.0579 20.0765L17.4329 23.6453C16.088 24.5596 14.1562 23.6116 14.1562 21.8874V5.79375C14.1562 4.96402 14.6204 4.24683 15.4359 3.83906ZM16.9688 13.4062C16.9688 13.7949 17.2832 14.1094 17.6719 14.1094C18.0605 14.1094 18.375 13.7949 18.375 13.4062V10.5938C18.375 10.2051 18.0605 9.89062 17.6719 9.89062C17.2832 9.89062 16.9688 10.2051 16.9688 10.5938V13.4062Z" fill="#FF2D55"/>
                    <path d="M0.528111 11.4398L5.47155 7.98862C5.91452 7.54566 6.67188 7.85934 6.67188 8.48573V10.5937H12.2969C12.6852 10.5937 13 10.9085 13 11.2969V12.7031C13 13.0914 12.6852 13.4062 12.2969 13.4062H6.67188V15.5142C6.67188 16.1406 5.91461 16.4543 5.47164 16.0114L0.528111 12.56C0.157375 12.2787 0.157375 11.7212 0.528111 11.4398Z" fill="#FF2D55"/>
                    <path d="M13 14.8125V21.1875H10.8906C9.72747 21.1875 8.78125 20.2413 8.78125 19.0781V14.8125C11.2471 14.8125 10.5296 14.8125 13 14.8125Z" fill="#D2D6DB"/>
                    <path d="M10.9174 0H20L15.0865 2.57972L15.0838 2.58108C15.0837 2.58117 15.0836 2.58131 15.0836 2.58131L15.0591 2.59411C13.7957 3.22519 13.0267 4.42612 13.0267 5.79394V9.1875C10.5563 9.1875 11.2738 9.1875 8.80794 9.1875V2.10938C8.80799 0.946219 9.75416 0 10.9174 0Z" fill="#D2D6DB"/>
                </g>
                <defs>
                    <clipPath id="clip0_625_359">
                        <rect width="24" height="24" fill="white"/>
                    </clipPath>
                </defs>
            </svg>
        )
    },
})
