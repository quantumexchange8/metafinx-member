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

export const DashboardIconInactive = defineComponent({
    setup() {
        return () => (
            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="24" viewBox="0 0 25 24" fill="none">
                <g clip-path="url(#clip0_457_21823)">
                    <path d="M9.51788 0H2.6274C1.6198 0 0.800171 0.819634 0.800171 1.82723V8.71771C0.800171 9.7253 1.6198 10.5449 2.6274 10.5449H9.51788C10.5255 10.5449 11.3451 9.7253 11.3451 8.71771V1.82723C11.3449 0.819634 10.5255 0 9.51788 0Z" fill="#D2D6DB"/>
                    <path d="M22.9728 0H16.0824C15.0748 0 14.2551 0.819634 14.2551 1.82723V8.71771C14.2551 9.7253 15.0748 10.5449 16.0824 10.5449H22.9728C23.9804 10.5449 24.8001 9.7253 24.8001 8.71771V1.82723C24.8001 0.819634 23.9804 0 22.9728 0Z" fill="#D2D6DB"/>
                    <path d="M9.51788 13.4551H2.6274C1.6198 13.4551 0.800171 14.2746 0.800171 15.2822V22.1727C0.800171 23.1803 1.6198 23.9999 2.6274 23.9999H9.51788C10.5255 23.9999 11.3451 23.1803 11.3451 22.1727V15.2822C11.3449 14.2746 10.5255 13.4551 9.51788 13.4551Z" fill="#D2D6DB"/>
                    <path d="M22.9728 13.4551H16.0824C15.0748 13.4551 14.2551 14.2747 14.2551 15.2823V22.1728C14.2551 23.1804 15.0748 24 16.0824 24H22.9728C23.9804 23.9999 24.8001 23.1803 24.8001 22.1727V15.2822C24.8001 14.2746 23.9804 13.4551 22.9728 13.4551Z" fill="#D2D6DB"/>
                </g>
                <defs>
                    <clipPath id="clip0_457_21823">
                    <rect width="24" height="24" fill="white" transform="translate(0.800049)"/>
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

export const WalletIcon = defineComponent({
    setup() {
        return () => (
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g clip-path="url(#clip0_636_7714)">
                    <path d="M15.6106 6.20664C17.3246 6.20664 18.714 4.81723 18.714 3.10332C18.714 1.3894 17.3246 0 15.6106 0C13.8967 0 12.5073 1.3894 12.5073 3.10332C12.5073 4.81723 13.8967 6.20664 15.6106 6.20664Z" fill="#D2D6DB"/>
                    <path d="M8.55522 3.52393C6.84128 3.52393 5.4519 4.9133 5.4519 6.62724C5.4519 6.75892 5.46105 6.88839 5.47698 7.01574H11.6335C11.6494 6.88843 11.6585 6.75892 11.6585 6.62724C11.6585 4.91335 10.2692 3.52393 8.55522 3.52393Z" fill="#D2D6DB"/>
                    <path d="M14.669 12.1916H21.349V9.62477C21.349 8.99383 20.8375 8.48242 20.2066 8.48242H2.18214C1.5512 8.48242 1.03979 8.99392 1.03979 9.62477V22.8578C1.03979 23.4887 1.5512 24.0001 2.18214 24.0001H20.2066C20.8376 24.0001 21.349 23.4887 21.349 22.8578V20.291H14.669C13.2929 20.291 12.1733 19.1714 12.1733 17.7952V14.6873C12.1732 13.3112 13.2928 12.1916 14.669 12.1916Z" fill="#FF2D55"/>
                    <path d="M22.0243 13.7515H14.669C14.1521 13.7515 13.7332 14.1704 13.7332 14.6873V17.7952C13.7332 18.3121 14.1521 18.7311 14.669 18.7311H22.0243C22.5412 18.7311 22.9601 18.3121 22.9601 17.7952V14.6873C22.9601 14.1704 22.5411 13.7515 22.0243 13.7515ZM16.2173 17.3398C15.6105 17.3398 15.1187 16.848 15.1187 16.2413C15.1187 15.6346 15.6105 15.1428 16.2173 15.1428C16.824 15.1428 17.3158 15.6346 17.3158 16.2413C17.3158 16.848 16.824 17.3398 16.2173 17.3398Z" fill="#D2D6DB"/>
                </g>
                <defs>
                    <clipPath id="clip0_636_7714">
                        <rect width="24" height="24" fill="white"/>
                    </clipPath>
                </defs>
            </svg>
        )
    },
})

export const DepositIcon = defineComponent({
    setup() {
        return () => (
            <svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M14.5001 8.33317C16.1109 8.33317 17.4167 7.02733 17.4167 5.4165C17.4167 3.80567 16.1109 2.49984 14.5001 2.49984C12.8893 2.49984 11.5834 3.80567 11.5834 5.4165C11.5834 7.02733 12.8893 8.33317 14.5001 8.33317Z" fill="white"/>
                <path d="M12.0247 6.95972C11.5476 7.29979 10.9639 7.49984 10.3334 7.49984C8.72258 7.49984 7.41675 6.194 7.41675 4.58317C7.41675 2.97234 8.72258 1.6665 10.3334 1.6665C11.3776 1.6665 12.2936 2.21518 12.8088 3.03996M17.4167 5.4165C17.4167 7.02733 16.1109 8.33317 14.5001 8.33317C12.8893 8.33317 11.5834 7.02733 11.5834 5.4165C11.5834 3.80567 12.8893 2.49984 14.5001 2.49984C16.1109 2.49984 17.4167 3.80567 17.4167 5.4165Z" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M11.8212 14.2084H13.8447C14.2203 14.2084 14.5854 14.0902 14.8855 13.8709L16.9809 12.3421C17.5039 11.9607 18.236 12.0114 18.6982 12.4611C19.2117 12.9605 19.2117 13.7696 18.6982 14.2683L16.9003 16.0172C16.4163 16.488 15.7988 16.8095 15.1275 16.9403L12.5862 17.4347C12.0823 17.5325 11.5628 17.5207 11.0641 17.3992L8.76568 16.8407C8.49074 16.7732 8.20886 16.7395 7.92524 16.7395H5.75" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M11.8212 14.2084L13.1092 14.2083C13.8352 14.2083 14.4232 13.6363 14.4232 12.9301V12.6745C14.4232 12.0881 14.013 11.5768 13.4284 11.4351L11.4405 10.9516C11.117 10.8732 10.7857 10.8335 10.4526 10.8335C9.64861 10.8335 8.19324 11.4992 8.19324 11.4992L5.75 12.5209" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M4.41675 18.3335C4.88346 18.3335 5.11681 18.3335 5.29507 18.2427C5.45188 18.1628 5.57936 18.0353 5.65925 17.8785C5.75008 17.7002 5.75008 17.4669 5.75008 17.0002V12.1668C5.75008 11.7001 5.75008 11.4668 5.65925 11.2885C5.57936 11.1317 5.45188 11.0042 5.29507 10.9243C5.11681 10.8335 4.88346 10.8335 4.41675 10.8335L3.75008 10.8335C3.28337 10.8335 3.05002 10.8335 2.87176 10.9243C2.71495 11.0042 2.58747 11.1317 2.50758 11.2885C2.41675 11.4668 2.41675 11.7001 2.41675 12.1668L2.41675 17.0002C2.41675 17.4669 2.41675 17.7002 2.50758 17.8785C2.58747 18.0353 2.71495 18.1628 2.87176 18.2427C3.05002 18.3335 3.28337 18.3335 3.75008 18.3335H4.41675Z" fill="white" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        )
    },
})

export const WithdrawalIcon = defineComponent({
    setup() {
        return () => (
            <svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g clip-path="url(#clip0_636_3173)">
                    <path d="M5.25008 9.16683V12.5002M15.2501 7.50016V10.8335M14.4167 3.3335C16.4573 3.3335 17.561 3.6458 18.1102 3.88803C18.1833 3.92029 18.2199 3.93642 18.3254 4.03714C18.3887 4.09751 18.5042 4.27466 18.5339 4.3569C18.5834 4.49411 18.5834 4.56911 18.5834 4.71911V13.6761C18.5834 14.4334 18.5834 14.8121 18.4699 15.0067C18.3543 15.2047 18.2429 15.2967 18.0267 15.3728C17.8141 15.4476 17.3851 15.3652 16.5269 15.2003C15.9262 15.0849 15.2138 15.0002 14.4167 15.0002C11.9167 15.0002 9.41675 16.6668 6.08341 16.6668C4.04283 16.6668 2.93914 16.3545 2.38998 16.1123C2.31685 16.08 2.28028 16.0639 2.17475 15.9632C2.11149 15.9028 1.99601 15.7257 1.96631 15.6434C1.91675 15.5062 1.91675 15.4312 1.91675 15.2812L1.91675 6.32421C1.91675 5.5669 1.91675 5.18825 2.03031 4.99362C2.14583 4.79563 2.25724 4.70359 2.47346 4.62751C2.68603 4.55271 3.11511 4.63515 3.97327 4.80004C4.57393 4.91545 5.28631 5.00016 6.08341 5.00016C8.58342 5.00016 11.0834 3.3335 14.4167 3.3335ZM12.3334 10.0002C12.3334 11.1508 11.4007 12.0835 10.2501 12.0835C9.09949 12.0835 8.16675 11.1508 8.16675 10.0002C8.16675 8.84957 9.09949 7.91683 10.2501 7.91683C11.4007 7.91683 12.3334 8.84957 12.3334 10.0002Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </g>
                <defs>
                    <clipPath id="clip0_636_3173">
                        <rect width="20" height="20" fill="white" transform="translate(0.25)"/>
                    </clipPath>
                </defs>
            </svg>
        )
    },
})

export const EarnIcon = defineComponent({
    setup() {
        return () => (
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g clip-path="url(#clip0_338_5953)">
                    <path d="M14.8124 1.05745C14.3153 0.575391 13.5456 0 12.703 0C11.3359 0 10.1604 1.51472 10.0313 1.68778C9.87132 1.90064 9.84592 2.18559 9.9647 2.42386L12.2684 7.07812H17.3561L19.6602 2.42386C19.779 2.18559 19.7536 1.90064 19.5936 1.68778C19.4644 1.51472 18.2889 0 16.9218 0C16.0793 0 15.3095 0.575391 14.8124 1.05745Z" fill="#D2D6DB"/>
                    <path d="M15.5154 8.48438V8.60522C15.5154 9.68395 15.9528 10.74 16.7157 11.5028C16.9903 11.7775 16.9903 12.2225 16.7157 12.4971C16.441 12.7718 15.996 12.7718 15.7214 12.4971C15.3623 12.1387 15.0574 11.7315 14.8123 11.2913C14.5671 11.7315 14.2623 12.1387 13.9031 12.4971C13.6285 12.7718 13.1835 12.7718 12.9088 12.4971C12.6342 12.2225 12.6342 11.7775 12.9088 11.5028C13.6718 10.74 14.1092 9.68395 14.1092 8.60522V8.48438H11.4747C9.85283 10.0172 8.24361 12.1006 7.16455 14.1094H13.4061C14.5692 14.1094 15.5154 15.0556 15.5154 16.2188V23.2969C15.5154 23.5461 15.4644 23.7779 15.3846 24H16.3215C20.3705 24 23.9998 23.0973 23.9998 19.0312C23.9998 15.7783 20.8272 11.148 18.1 8.48438H15.5154Z" fill="#FF2D55"/>
                    <path d="M7.07812 20.4375C7.46645 20.4375 7.78125 20.1227 7.78125 19.7344C7.78125 19.346 7.46645 19.0312 7.07812 19.0312C6.6898 19.0312 6.375 19.346 6.375 19.7344C6.375 20.1227 6.6898 20.4375 7.07812 20.4375Z" fill="#D2D6DB"/>
                    <path d="M0.703125 24H13.4062C13.7949 24 14.1094 23.6855 14.1094 23.2969V16.2188C14.1094 15.8301 13.7949 15.5156 13.4062 15.5156H0.703125C0.314484 15.5156 0 15.8301 0 16.2188V23.2969C0 23.6855 0.314484 24 0.703125 24ZM11.2969 19.0312C11.6852 19.0312 12 19.346 12 19.7344C12 20.1227 11.6852 20.4375 11.2969 20.4375C10.9086 20.4375 10.5938 20.1227 10.5938 19.7344C10.5938 19.346 10.9086 19.0312 11.2969 19.0312ZM7.07812 17.625C8.24128 17.625 9.1875 18.5712 9.1875 19.7344C9.1875 20.8975 8.24128 21.8438 7.07812 21.8438C5.91497 21.8438 4.96875 20.8975 4.96875 19.7344C4.96875 18.5712 5.91497 17.625 7.07812 17.625ZM2.85938 19.0312C3.24769 19.0312 3.5625 19.346 3.5625 19.7344C3.5625 20.1227 3.24769 20.4375 2.85938 20.4375C2.47106 20.4375 2.15625 20.1227 2.15625 19.7344C2.15625 19.346 2.47106 19.0312 2.85938 19.0312Z" fill="#FF2D55"/>
                </g>
                <defs>
                    <clipPath id="clip0_338_5953">
                        <rect width="24" height="24" fill="white"/>
                    </clipPath>
                </defs>
            </svg>
        )
    },
})

export const Wallet = defineComponent({
    setup() {
        return () => (
            <svg width="140" height="140" viewBox="0 0 140 140" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g opacity="0.4" clip-path="url(#clip0_529_20106)">
                    <path d="M122.279 61.4483L23.7982 44.0835M20.3253 63.7796L37.7892 66.859C40.4492 67.328 41.7791 67.5625 43.0247 68.0185C44.1303 68.4233 45.1844 68.9568 46.1653 69.608C47.2703 70.3417 48.2468 71.2746 50.1999 73.1403L52.7267 75.5542C54.6797 77.4199 55.6562 78.3527 56.7613 79.0864C57.7421 79.7377 58.7962 80.2712 59.9018 80.6759C61.1474 81.1319 62.4774 81.3664 65.1373 81.8355L70.521 82.7848C73.181 83.2538 74.5109 83.4883 75.8374 83.4858C77.0147 83.4836 78.1877 83.3428 79.3322 83.0663C80.6215 82.7548 81.8582 82.2122 84.3315 81.1269L87.5316 79.7229C90.0049 78.6377 91.2416 78.0951 92.531 77.7835C93.6754 77.507 94.8484 77.3662 96.0257 77.364C97.3522 77.3615 98.6822 77.5961 101.342 78.0651L118.806 81.1444M24.927 37.6822L16.5918 84.953C15.6193 90.4684 15.1331 93.2261 15.835 95.522C16.4524 97.5415 17.6933 99.3137 19.3799 100.585C21.2972 102.029 24.055 102.516 29.5704 103.488L96.5373 115.296C102.053 116.269 104.81 116.755 107.106 116.053C109.126 115.436 110.898 114.195 112.169 112.508C113.614 110.591 114.1 107.833 115.073 102.318L123.408 55.047C124.38 49.5316 124.867 46.7739 124.165 44.478C123.547 42.4585 122.306 40.6863 120.62 39.4154C118.702 37.9705 115.945 37.4843 110.429 36.5117L43.4623 24.7037C37.9468 23.7311 35.1891 23.2449 32.8932 23.9468C30.8737 24.5642 29.1015 25.8051 27.8306 27.4917C26.3857 29.4091 25.8995 32.1668 24.927 37.6822Z" stroke="white" stroke-width="10" stroke-linecap="round" stroke-linejoin="round"/>
                </g>
                <defs>
                    <clipPath id="clip0_529_20106">
                        <rect width="120" height="120" fill="white" transform="translate(21.3303 0.492676) rotate(10)"/>
                    </clipPath>
                </defs>
            </svg>
        )
    },
})

export const CloudDownloadIcon = defineComponent({
    setup() {
        return () => (
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M3.33341 13.5352C2.32843 12.8625 1.66675 11.7168 1.66675 10.4167C1.66675 8.46369 3.15967 6.85941 5.06653 6.68281C5.45659 4.31011 7.51695 2.5 10.0001 2.5C12.4832 2.5 14.5436 4.31011 14.9336 6.68281C16.8405 6.85941 18.3334 8.46369 18.3334 10.4167C18.3334 11.7168 17.6717 12.8625 16.6667 13.5352M6.66675 14.1667L10.0001 17.5M10.0001 17.5L13.3334 14.1667M10.0001 17.5V10" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        )
    },
})

export const InternalWalletIcon = defineComponent({
    setup() {
        return () => (
            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M11 4.25H1M1 6.25H2.77334C3.04343 6.25 3.17848 6.25 3.30907 6.27328C3.42498 6.29394 3.53805 6.32817 3.64595 6.37528C3.76752 6.42835 3.87989 6.50326 4.10462 6.65308L4.39538 6.84692C4.62011 6.99674 4.73248 7.07165 4.85405 7.12472C4.96195 7.17183 5.07502 7.20606 5.19093 7.22672C5.32152 7.25 5.45657 7.25 5.72666 7.25H6.27334C6.54343 7.25 6.67848 7.25 6.80907 7.22672C6.92498 7.20606 7.03805 7.17183 7.14595 7.12472C7.26752 7.07165 7.37989 6.99674 7.60462 6.84692L7.89538 6.65308C8.12011 6.50326 8.23248 6.42835 8.35405 6.37528C8.46195 6.32817 8.57502 6.29394 8.69093 6.27328C8.82152 6.25 8.95657 6.25 9.22666 6.25H11M1 3.6L1 8.4C1 8.96005 1 9.24008 1.10899 9.45399C1.20487 9.64215 1.35785 9.79513 1.54601 9.89101C1.75992 10 2.03995 10 2.6 10L9.4 10C9.96005 10 10.2401 10 10.454 9.89101C10.6422 9.79513 10.7951 9.64215 10.891 9.45399C11 9.24008 11 8.96005 11 8.4V3.6C11 3.03995 11 2.75992 10.891 2.54601C10.7951 2.35785 10.6422 2.20487 10.454 2.10899C10.2401 2 9.96005 2 9.4 2L2.6 2C2.03995 2 1.75992 2 1.54601 2.10899C1.35785 2.20487 1.20487 2.35785 1.10899 2.54601C1 2.75992 1 3.03995 1 3.6Z" stroke="white" stroke-width="0.8" stroke-linecap="round" stroke-linejoin="round"/>
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

export const InactiveEarnIcon = defineComponent({
    setup() {
        return () => (
            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="24" viewBox="0 0 25 24" fill="none">
                <g clip-path="url(#clip0_438_17553)">
                    <path d="M15.2126 1.05745C14.7155 0.575391 13.9457 0 13.1032 0C11.7361 0 10.5605 1.51472 10.4315 1.68778C10.2715 1.90064 10.2461 2.18559 10.3648 2.42386L12.6686 7.07812H17.7563L20.0603 2.42386C20.1791 2.18559 20.1537 1.90064 19.9937 1.68778C19.8646 1.51472 18.689 0 17.3219 0C16.4794 0 15.7097 0.575391 15.2126 1.05745Z" fill="#D2D6DB"/>
                    <path d="M15.9158 8.48438V8.60522C15.9158 9.68395 16.3532 10.74 17.1161 11.5028C17.3907 11.7775 17.3907 12.2225 17.1161 12.4971C16.8414 12.7718 16.3964 12.7718 16.1218 12.4971C15.7627 12.1387 15.4578 11.7315 15.2126 11.2913C14.9675 11.7315 14.6627 12.1387 14.3035 12.4971C14.0289 12.7718 13.5839 12.7718 13.3092 12.4971C13.0346 12.2225 13.0346 11.7775 13.3092 11.5028C14.0722 10.74 14.5096 9.68395 14.5096 8.60522V8.48438H11.8751C10.2532 10.0172 8.644 12.1006 7.56494 14.1094H13.8064C14.9696 14.1094 15.9158 15.0556 15.9158 16.2188V23.2969C15.9158 23.5461 15.8648 23.7779 15.785 24H16.7219C20.7708 24 24.4002 23.0973 24.4002 19.0312C24.4002 15.7783 21.2276 11.148 18.5004 8.48438H15.9158Z" fill="#D2D6DB"/>
                    <path d="M7.47827 20.4375C7.8666 20.4375 8.1814 20.1227 8.1814 19.7344C8.1814 19.346 7.8666 19.0312 7.47827 19.0312C7.08995 19.0312 6.77515 19.346 6.77515 19.7344C6.77515 20.1227 7.08995 20.4375 7.47827 20.4375Z" fill="#D2D6DB"/>
                    <path d="M1.10327 24H13.8064C14.195 24 14.5095 23.6855 14.5095 23.2969V16.2188C14.5095 15.8301 14.195 15.5156 13.8064 15.5156H1.10327C0.714631 15.5156 0.400146 15.8301 0.400146 16.2188V23.2969C0.400146 23.6855 0.714631 24 1.10327 24ZM11.697 19.0312C12.0853 19.0312 12.4001 19.346 12.4001 19.7344C12.4001 20.1227 12.0853 20.4375 11.697 20.4375C11.3087 20.4375 10.9939 20.1227 10.9939 19.7344C10.9939 19.346 11.3087 19.0312 11.697 19.0312ZM7.47827 17.625C8.64143 17.625 9.58765 18.5712 9.58765 19.7344C9.58765 20.8975 8.64143 21.8438 7.47827 21.8438C6.31512 21.8438 5.3689 20.8975 5.3689 19.7344C5.3689 18.5712 6.31512 17.625 7.47827 17.625ZM3.25952 19.0312C3.64783 19.0312 3.96265 19.346 3.96265 19.7344C3.96265 20.1227 3.64783 20.4375 3.25952 20.4375C2.87121 20.4375 2.5564 20.1227 2.5564 19.7344C2.5564 19.346 2.87121 19.0312 3.25952 19.0312Z" fill="#D2D6DB"/>
                </g>
                <defs>
                    <clipPath id="clip0_438_17553">
                <rect width="24" height="24" fill="white" transform="translate(0.400146)"/>
                    </clipPath>
                </defs>
            </svg>
        )
    }
})

export const InactiveWalletIcon = defineComponent({
    setup() {
        return () => (
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                <g clip-path="url(#clip0_438_17561)">
                    <path d="M15.6106 6.20664C17.3246 6.20664 18.714 4.81723 18.714 3.10332C18.714 1.3894 17.3246 0 15.6106 0C13.8967 0 12.5073 1.3894 12.5073 3.10332C12.5073 4.81723 13.8967 6.20664 15.6106 6.20664Z" fill="#D2D6DB"/>
                    <path d="M8.55522 3.52344C6.84128 3.52344 5.4519 4.91282 5.4519 6.62676C5.4519 6.75843 5.46105 6.8879 5.47698 7.01526H11.6335C11.6494 6.88794 11.6585 6.75843 11.6585 6.62676C11.6585 4.91286 10.2692 3.52344 8.55522 3.52344Z" fill="#D2D6DB"/>
                    <path d="M14.669 12.1916H21.349V9.62477C21.349 8.99383 20.8375 8.48242 20.2066 8.48242H2.18214C1.5512 8.48242 1.03979 8.99392 1.03979 9.62477V22.8578C1.03979 23.4887 1.5512 24.0001 2.18214 24.0001H20.2066C20.8376 24.0001 21.349 23.4887 21.349 22.8578V20.291H14.669C13.2929 20.291 12.1733 19.1714 12.1733 17.7952V14.6873C12.1732 13.3112 13.2928 12.1916 14.669 12.1916Z" fill="#D2D6DB"/>
                    <path d="M22.0243 13.752H14.669C14.1521 13.752 13.7332 14.1709 13.7332 14.6878V17.7957C13.7332 18.3126 14.1521 18.7316 14.669 18.7316H22.0243C22.5412 18.7316 22.9601 18.3126 22.9601 17.7957V14.6878C22.9601 14.1709 22.5411 13.752 22.0243 13.752ZM16.2173 17.3403C15.6105 17.3403 15.1187 16.8485 15.1187 16.2418C15.1187 15.6351 15.6105 15.1433 16.2173 15.1433C16.824 15.1433 17.3158 15.6351 17.3158 16.2418C17.3158 16.8485 16.824 17.3403 16.2173 17.3403Z" fill="#D2D6DB"/>
                </g>
                <defs>
                    <clipPath id="clip0_438_17561">
                    <rect width="24" height="24" fill="white"/>
                    </clipPath>
                </defs>
            </svg>
        )
    }
})

export const InactiveAffiliateIcon = defineComponent({
    setup() {
        return () => (
            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="24" viewBox="0 0 25 24" fill="none">
                <g clip-path="url(#clip0_438_17576)">
                    <path d="M3.93914 17.3301C2.09799 17.3301 0.600098 18.828 0.600098 20.6692C0.78352 25.0989 7.09547 25.0976 7.27824 20.6691C7.27824 18.828 5.78035 17.3301 3.93914 17.3301Z" fill="#D2D6DB"/>
                    <path d="M15.2111 2.61875C15.2111 1.17908 14.0398 0.0078125 12.6002 0.0078125C9.13637 0.15125 9.13736 5.08681 12.6002 5.22969C14.0398 5.22969 15.2111 4.05842 15.2111 2.61875Z" fill="#D2D6DB"/>
                    <path d="M8.83447 11.2039H16.3657C16.7541 11.2039 17.0689 10.8891 17.0689 10.5007V9.69764C16.8227 3.76776 8.37533 3.77236 8.13135 9.69764V10.5007C8.13135 10.8891 8.44616 11.2039 8.83447 11.2039Z" fill="#D2D6DB"/>
                    <path d="M21.2612 17.3301C19.42 17.3301 17.9221 18.828 17.9221 20.6691C18.1055 25.0989 24.4175 25.0975 24.6003 20.6691C24.6002 18.828 23.1023 17.3301 21.2612 17.3301Z" fill="#D2D6DB"/>
                    <path d="M4.64233 15.9759V14.9707H11.897V15.9759C12.354 15.9067 12.8464 15.9067 13.3033 15.9759V14.9707H20.558V15.9759C21.0149 15.9066 21.5073 15.9067 21.9642 15.976V14.2676C21.9642 13.8793 21.6494 13.5644 21.2611 13.5644H13.3033V12.6113H11.897V13.5644H3.93921C3.5509 13.5644 3.23608 13.8793 3.23608 14.2676V15.976C3.69302 15.9067 4.1854 15.9066 4.64233 15.9759Z" fill="#D2D6DB"/>
                    <path d="M12.6 17.3301C10.7589 17.3301 9.26099 18.828 9.26099 20.6691C9.44441 25.0989 15.7564 25.0975 15.9391 20.6691C15.9391 18.828 14.4412 17.3301 12.6 17.3301Z" fill="#D2D6DB"/>
                </g>
                <defs>
                    <clipPath id="clip0_438_17576">
                    <rect width="24" height="24" fill="white" transform="translate(0.600098)"/>
                    </clipPath>
                </defs>
            </svg>
        )
    }
})

export const InactiveReportIcon = defineComponent({
    setup() {
        return () => (
            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="24" viewBox="0 0 25 24" fill="none">
                <path d="M14.0984 0H10.3015C8.20789 0 6.50464 1.70662 6.50464 3.80433C6.50464 4.19339 6.81945 4.50881 7.20776 4.50881H17.1921C17.5805 4.50881 17.8953 4.19339 17.8953 3.80433C17.8953 1.70662 16.192 0 14.0984 0Z" fill="#D2D6DB"/>
                <path d="M19.6062 1.31445H18.6691C19.0722 2.05456 19.3015 2.90281 19.3015 3.8037C19.3015 4.96911 18.3552 5.9172 17.1921 5.9172H7.20776C6.04465 5.9172 5.09839 4.96906 5.09839 3.8037C5.09839 2.90281 5.3277 2.05461 5.73083 1.31445H4.7937C3.63059 1.31445 2.68433 2.26259 2.68433 3.42795V21.8859C2.68433 23.0512 3.63059 23.9994 4.7937 23.9994H19.6062C20.7693 23.9994 21.7156 23.0512 21.7156 21.8859V3.42795C21.7156 2.26255 20.7693 1.31445 19.6062 1.31445ZM9.72833 19.9731L8.19711 21.5073C7.92256 21.7824 7.47734 21.7825 7.20275 21.5073L6.45275 20.7559C6.17815 20.4807 6.17815 20.0347 6.45275 19.7596C6.7273 19.4844 7.17251 19.4844 7.44711 19.7596L7.69995 20.0129L8.73401 18.9769C9.00856 18.7017 9.45378 18.7017 9.72837 18.9769C10.0029 19.2519 10.0029 19.6979 9.72833 19.9731ZM9.72833 15.9339L8.19711 17.4681C7.92256 17.7432 7.47734 17.7433 7.20275 17.4681L6.45275 16.7167C6.17815 16.4416 6.17815 15.9955 6.45275 15.7204C6.7273 15.4452 7.17251 15.4452 7.44711 15.7204L7.69995 15.9737L8.73401 14.9377C9.00856 14.6625 9.45378 14.6625 9.72837 14.9377C10.0029 15.2127 10.0029 15.6588 9.72833 15.9339ZM8.38461 13.0846C8.11006 13.3598 7.66484 13.3598 7.39025 13.0846C7.11565 12.8095 7.11565 12.3635 7.39025 12.0883L9.9215 9.55212C10.196 9.27697 10.6413 9.27697 10.9159 9.55212L13.0437 11.684L14.7212 10.0032H14.6375C14.2491 10.0032 13.9343 9.68783 13.9343 9.29877C13.9343 8.9097 14.2491 8.59428 14.6375 8.59428C16.5737 8.59428 16.7387 8.51127 17.0034 8.90769C17.1649 9.14947 17.1218 9.23127 17.1218 11.0835C17.1218 11.4726 16.807 11.788 16.4187 11.788C16.0304 11.788 15.7156 11.4726 15.7156 11.0835V10.9996L13.5409 13.1785C13.2663 13.4536 12.8211 13.4537 12.5465 13.1785L10.4187 11.0466L8.38461 13.0846ZM17.4031 20.9465H11.7312C11.3429 20.9465 11.0281 20.6311 11.0281 20.2421C11.0281 19.853 11.3429 19.5376 11.7312 19.5376H17.4031C17.7914 19.5376 18.1062 19.853 18.1062 20.2421C18.1062 20.6311 17.7914 20.9465 17.4031 20.9465ZM17.4031 16.9074H11.7312C11.3429 16.9074 11.0281 16.592 11.0281 16.2029C11.0281 15.8138 11.3429 15.4984 11.7312 15.4984H17.4031C17.7914 15.4984 18.1062 15.8138 18.1062 16.2029C18.1062 16.592 17.7914 16.9074 17.4031 16.9074Z" fill="#D2D6DB"/>
            </svg>
        )
    }
})

export const PurseIcon = defineComponent({
    setup() {
        return () => (
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                <path d="M15.8334 8.3335L4.16678 8.3335M15.8334 8.3335L17.3869 10.9226C17.7351 11.5029 17.9091 11.793 17.9995 12.1053C18.0797 12.382 18.1113 12.6705 18.0932 12.9581C18.0727 13.2826 17.9657 13.6035 17.7517 14.2455L17.5785 14.7651C17.2506 15.7486 17.0867 16.2404 16.7827 16.6039C16.5142 16.925 16.1694 17.1735 15.7799 17.3267C15.3388 17.5002 14.8205 17.5002 13.7837 17.5002L6.21648 17.5002C5.17976 17.5002 4.66139 17.5002 4.22033 17.3267C3.83084 17.1735 3.48605 16.925 3.21756 16.6039C2.91351 16.2404 2.74959 15.7486 2.42175 14.7651L2.24856 14.2455C2.03457 13.6035 1.92758 13.2825 1.90707 12.9581C1.88891 12.6705 1.92058 12.382 2.00069 12.1053C2.09109 11.793 2.26517 11.5029 2.61332 10.9226L4.16678 8.3335M15.8334 8.3335L16.1812 7.11646C16.346 6.53947 16.4285 6.25097 16.3637 6.02273C16.307 5.82279 16.1776 5.65126 16.0009 5.54179C15.7993 5.41683 15.4992 5.41683 14.8991 5.41683L5.10109 5.41683C4.50101 5.41683 4.20097 5.41683 3.9993 5.54179C3.82263 5.65126 3.69325 5.82279 3.63653 6.02273C3.57177 6.25097 3.6542 6.53947 3.81906 7.11646L4.16678 8.3335M10.0001 5.41683H7.05369C6.61955 5.41683 6.2032 5.24123 5.89622 4.92867C5.58924 4.61611 5.41678 4.19219 5.41678 3.75016C5.41678 3.30814 5.58924 2.88421 5.89622 2.57165C6.2032 2.25909 6.61955 2.0835 7.05369 2.0835C9.34535 2.0835 10.0001 5.41683 10.0001 5.41683ZM10.0001 5.41683H12.9465C13.3807 5.41683 13.797 5.24123 14.104 4.92867C14.411 4.61611 14.5834 4.19219 14.5834 3.75016C14.5834 3.30814 14.411 2.88421 14.104 2.57165C13.797 2.25909 13.3807 2.0835 12.9465 2.0835C10.6549 2.0835 10.0001 5.41683 10.0001 5.41683Z" stroke="white" stroke-width="1.67" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        )
    }
})

export const checkCircle = defineComponent({
    setup() {
        return () => (
            <svg width="56" height="56" viewBox="0 0 56 56" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="4" y="4" width="48" height="48" rx="24" fill="#D1FADF"/>
                <path d="M38 27.0799V27.9999C37.9988 30.1563 37.3005 32.2545 36.0093 33.9817C34.7182 35.7088 32.9033 36.9723 30.8354 37.5838C28.7674 38.1952 26.5573 38.1218 24.5345 37.3744C22.5117 36.6271 20.7847 35.246 19.611 33.4369C18.4373 31.6279 17.8798 29.4879 18.0217 27.3362C18.1636 25.1844 18.9972 23.1362 20.3983 21.4969C21.7994 19.8577 23.6928 18.7152 25.7962 18.24C27.8996 17.7648 30.1003 17.9822 32.07 18.8599M38 19.9999L28 30.0099L25 27.0099" stroke="#039855" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <rect x="4" y="4" width="48" height="48" rx="24" stroke="#ECFDF3" stroke-width="8"/>
            </svg>

        )
    }
})

export const alertTriangle = defineComponent({
    setup() {
        return () => (
            <svg width="56" height="56" viewBox="0 0 56 56" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="4" y="4" width="48" height="48" rx="24" fill="#FEF0C7"/>
                <path d="M27.9998 24.0002V28.0002M27.9998 32.0002H28.0098M26.2898 18.8602L17.8198 33.0002C17.6451 33.3026 17.5527 33.6455 17.5518 33.9947C17.5508 34.3439 17.6413 34.6873 17.8142 34.9907C17.9871 35.2941 18.2365 35.547 18.5375 35.7241C18.8385 35.9012 19.1806 35.9964 19.5298 36.0002H36.4698C36.819 35.9964 37.1611 35.9012 37.4621 35.7241C37.7631 35.547 38.0124 35.2941 38.1854 34.9907C38.3583 34.6873 38.4488 34.3439 38.4478 33.9947C38.4468 33.6455 38.3544 33.3026 38.1798 33.0002L29.7098 18.8602C29.5315 18.5663 29.2805 18.3233 28.981 18.1547C28.6814 17.9861 28.3435 17.8975 27.9998 17.8975C27.656 17.8975 27.3181 17.9861 27.0186 18.1547C26.7191 18.3233 26.468 18.5663 26.2898 18.8602Z" stroke="#DC6803" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <rect x="4" y="4" width="48" height="48" rx="24" stroke="#FFFAEB" stroke-width="8"/>
            </svg>
        )
    }
})
