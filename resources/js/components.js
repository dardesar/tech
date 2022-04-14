import {
    TInput,
    TTextarea,
    TRadio,
    TCheckbox,
    TButton,
    TDropdown,
    TRichSelect,
    TDatepicker,
    TToggle,
} from 'vue-tailwind/dist/components';

export const vueComponentSettings = {
    theme: {
        extend: {
            colors: {
                'regal-blue': '#243c5a',
            }
        }
    },
    't-input': {
        component: TInput,
        props: {
            classes: 'block w-full mt-1 text-sm border-gray-300 dark:border-gray-600 rounded-sm dark:bg-gray-700 focus:border-gray-300 focus:outline-none focus:shadow-none dark:text-gray-300 dark:focus:shadow-outline-gray form-input'
        }
    },
    't-textarea': {
        component: TTextarea,
        props: {
            classes: 'block w-full mt-1 text-sm border-gray-300 dark:border-gray-600 rounded-sm dark:bg-gray-700 focus:border-gray-300 focus:outline-none focus:shadow-none dark:text-gray-300 dark:focus:shadow-outline-gray form-input'
        }
    },
    't-checkbox': {
        component: TCheckbox,
        props: {
            classes: 'form-checkbox rounded-sm border-gray-300 focus:border-gray-300 focus:outline-none focus:shadow-none dark:text-gray-300 dark:focus:shadow-outline-gray'
        }
    },
    't-radio': {
        component: TRadio,
        props: {
            classes: 'form-checkbox focus:outline-none focus:ring-0 focus:border-none checked:text-indigo-500 focus:text-indigo-500 checked:text-indigo-500 border-gray-300 focus:border-gray-300 focus:outline-none focus:shadow-none dark:text-gray-300 dark:focus:shadow-outline-gray text-indigo-500 cursor-pointer'
        }
    },
    't-button': {
        component: TButton,
        props: {
            classes: 'block w-full px-1 py-1 text-sm font-medium leading-5 text-center text-white transition-colors duration-150 bg-gray-600 border border-transparent rounded-md active:bg-gray-600 hover:bg-gray-700 focus:outline-none'
        }
    },
    't-button-green': {
        component: TButton,
        props: {
            classes: 'filter-btn flex justify-between items-center py-1 border-radius-xl px-3 bg-green-500 text-sm rounded text-gray-100 cursor-pointer hover:bg-green-600 hover:text-gray-100 focus:outline-none'
        }
    },
    't-button-red': {
        component: TButton,
        props: {
            classes: 'flex justify-between items-center py-1 border-radius-xl px-3 bg-red-500 text-sm rounded text-gray-100 cursor-pointer hover:bg-red-600 hover:text-gray-100 focus:outline-none'
        }
    },
    't-rich-select': {
        component: TRichSelect,
        props: {
            fixedClasses: {
                wrapper: 'relative',
                buttonWrapper: 'inline-block relative w-full',
                selectButton: 'w-full flex text-left mt-1 justify-between items-center px-3 py-2 transition duration-100 ease-in-out border rounded shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none focus:ring-opacity-50 disabled:opacity-50 disabled:cursor-not-allowed',
                selectButtonLabel: 'block truncate',
                selectButtonPlaceholder: 'block truncate',
                selectButtonIcon: 'fill-current flex-shrink-0 ml-1 h-4 w-4',
                selectButtonClearButton: 'rounded flex flex-shrink-0 items-center justify-center absolute right-0 top-0 m-2 h-6 w-6 transition duration-100 ease-in-out',
                selectButtonClearIcon: 'fill-current h-3 w-3',
                dropdown: 'absolute w-full z-10 -mt-1 absolute border-b border-l border-r rounded-b shadow-sm z-10',
                dropdownFeedback: '',
                loadingMoreResults: '',
                optionsList: 'overflow-auto',
                searchWrapper: 'inline-block w-full',
                searchBox: 'inline-block w-full',
                optgroup: '',
                option: 'cursor-pointer',
                disabledOption: 'opacity-50 cursor-not-allowed',
                highlightedOption: 'cursor-pointer',
                selectedOption: 'cursor-pointer',
                selectedHighlightedOption: 'cursor-pointer',
                optionContent: '',
                optionLabel: 'truncate block',
                selectedIcon: 'fill-current h-4 w-4',
                enterClass: '',
                enterActiveClass: '',
                enterToClass: '',
                leaveClass: '',
                leaveActiveClass: '',
                leaveToClass: ''
            },
            classes: {
                wrapper: '',
                buttonWrapper: '',
                selectButton: 'bg-white border-gray-300',
                selectButtonLabel: '',
                selectButtonPlaceholder: 'text-gray-400',
                selectButtonIcon: 'text-gray-600',
                selectButtonClearButton: 'hover:bg-blue-100 text-gray-600',
                selectButtonClearIcon: '',
                dropdown: 'bg-white border-gray-300',
                dropdownFeedback: 'pb-2 px-3 text-gray-400 text-sm',
                loadingMoreResults: 'pb-2 px-3 text-gray-400 text-sm',
                optionsList: '',
                searchWrapper: 'p-2 placeholder-gray-400',
                searchBox: 'px-3 py-2 bg-gray-50 text-sm rounded border focus:outline-none focus:shadow-outline border-gray-300',
                optgroup: 'text-gray-400 uppercase text-xs py-1 px-2 font-semibold',
                option: '',
                disabledOption: '',
                highlightedOption: 'bg-blue-100',
                selectedOption: 'font-semibold bg-gray-100 bg-blue-500 font-semibold text-white',
                selectedHighlightedOption: 'font-semibold bg-gray-100 bg-blue-600 font-semibold text-white',
                optionContent: 'flex justify-between items-center px-3 py-2',
                optionLabel: '',
                selectedIcon: '',
                enterClass: '',
                enterActiveClass: 'opacity-0 transition ease-out duration-100',
                enterToClass: 'opacity-100',
                leaveClass: 'transition ease-in opacity-100',
                leaveActiveClass: '',
                leaveToClass: 'opacity-0 duration-75'
            },
            variants: {
                danger: {
                    selectButton: 'border-red-300 bg-red-50 text-red-900',
                    selectButtonPlaceholder: 'text-red-200',
                    selectButtonIcon: 'text-red-500',
                    selectButtonClearButton: 'hover:bg-red-200 text-red-500',
                    dropdown: 'bg-red-50 border-red-300'
                },
                success: {
                    selectButton: 'border-green-300 bg-green-50 text-green-900',
                    selectButtonIcon: 'text-green-500',
                    selectButtonClearButton: 'hover:bg-green-200 text-green-500',
                    dropdown: 'bg-green-50 border-green-300'
                }
            }
        }
    },
    't-rich-user-select': {
        component: TRichSelect,
        props: {
            fixedClasses: {
                wrapper: 'relative',
                buttonWrapper: 'inline-block relative w-full',
                selectButton: 'filter-select w-full flex text-left mt-2 justify-between items-center px-3 py-2 transition duration-100 ease-in-out border rounded focus:border-gray-200 focus:ring-2 focus:ring-gray-200 focus:outline-none focus:ring-opacity-10 disabled:opacity-50 disabled:cursor-not-allowed',
                selectButtonLabel: 'block truncate',
                selectButtonPlaceholder: 'block truncate',
                selectButtonIcon: 'fill-current flex-shrink-0 ml-1 h-4 w-4',
                selectButtonClearButton: 'rounded flex flex-shrink-0 items-center justify-center absolute right-0 top-2 m-2 h-6 w-6 transition duration-100 ease-in-out',
                selectButtonClearIcon: 'fill-current h-3 w-3',
                dropdown: 'filter-select__option absolute w-full z-10 -mt-1 absolute border-b border-l border-r rounded-b shadow-sm z-10',
                dropdownFeedback: '',
                loadingMoreResults: '',
                optionsList: 'overflow-auto',
                searchWrapper: 'inline-block w-full',
                searchBox: 'inline-block w-full',
                optgroup: '',
                option: 'cursor-pointer',
                disabledOption: 'opacity-50 cursor-not-allowed',
                highlightedOption: 'cursor-pointer',
                selectedOption: 'cursor-pointer',
                selectedHighlightedOption: 'cursor-pointer',
                optionContent: '',
                optionLabel: 'truncate block',
                selectedIcon: 'fill-current h-4 w-4',
                enterClass: '',
                enterActiveClass: '',
                enterToClass: '',
                leaveClass: '',
                leaveActiveClass: '',
                leaveToClass: ''
            },
            classes: {
                wrapper: '',
                buttonWrapper: '',
                selectButton: 'bg-white border-gray-200',
                selectButtonLabel: '',
                selectButtonPlaceholder: 'text-gray-400',
                selectButtonIcon: 'text-gray-600',
                selectButtonClearButton: 'bth-clear hover:bg-yellow-100 text-gray-600',
                selectButtonClearIcon: '',
                dropdown: 'bg-white border-gray-300',
                dropdownFeedback: 'pb-2 px-3 text-gray-400 text-sm',
                loadingMoreResults: 'pb-2 px-3 text-gray-400 text-sm',
                optionsList: '',
                searchWrapper: 'p-2 placeholder-gray-400',
                searchBox: 'px-3 py-2 bg-gray-50 text-sm rounded border focus:outline-none focus:shadow-none border-gray-200',
                optgroup: 'text-gray-400 uppercase text-xs py-1 px-2 font-semibold',
                option: '',
                disabledOption: '',
                highlightedOption: 'bg-yellow-100 filter-hover',
                selectedOption: 'font-semibold bg-gray-100 filter-active bg-yellow-500 font-semibold text-white',
                selectedHighlightedOption: 'font-semibold bg-gray-100 filter-active bg-yellow-500 font-semibold text-white',
                optionContent: 'flex justify-between items-center px-3 py-2',
                optionLabel: '',
                selectedIcon: '',
                enterClass: '',
                enterActiveClass: 'opacity-0 transition ease-out duration-100',
                enterToClass: 'opacity-100',
                leaveClass: 'transition ease-in opacity-100',
                leaveActiveClass: '',
                leaveToClass: 'opacity-0 duration-75'
            },
            variants: {
                danger: {
                    selectButton: 'border-red-300 bg-red-50 text-red-900',
                    selectButtonPlaceholder: 'text-red-200',
                    selectButtonIcon: 'text-red-500',
                    selectButtonClearButton: 'hover:bg-red-200 text-red-500',
                    dropdown: 'bg-red-50 border-red-300'
                },
                success: {
                    selectButton: 'border-green-300 bg-green-50 text-green-900',
                    selectButtonIcon: 'text-green-500',
                    selectButtonClearButton: 'hover:bg-green-200 text-green-500',
                    dropdown: 'bg-green-50 border-green-300'
                }
            }
        }
    },
    't-toggle': {
        component: TToggle,
        props: {
            fixedClasses: {
                wrapper: 'relative inline-flex flex-shrink-0 cursor-pointer transition-colors ease-in-out duration-200',
                wrapperChecked: 'relative inline-flex flex-shrink-0 cursor-pointer transition-colors ease-in-out duration-200  border-2 border-transparent focus:ring-2 focus:ring-blue-500 focus:outline-none focus:ring-opacity-50',
                wrapperDisabled: 'relative inline-flex flex-shrink-0 cursor-pointer transition-colors ease-in-out duration-200 opacity-50 cursor-not-allowed',
                wrapperCheckedDisabled: 'relative inline-flex flex-shrink-0 cursor-pointer transition-colors ease-in-out duration-200 opacity-50 cursor-not-allowed border-2 border-transparent focus:ring-2 focus:ring-blue-500 focus:outline-none focus:ring-opacity-50',
                button: 'inline-block absolute transform translate-x-0 transition ease-in-out duration-200',
                buttonChecked: 'inline-block absolute transform translate-x-full transition ease-in-out duration-200',
                checkedPlaceholder: 'inline-block',
                uncheckedPlaceholder: 'inline-block'
            },
            classes: {
                wrapper: 'block bg-gray-100 rounded-full border-2 border-transparent focus:ring-2 focus:ring-blue-500 focus:outline-none focus:ring-opacity-50',
                wrapperChecked: 'bg-blue-500 rounded-full',
                wrapperDisabled: 'bg-gray-100 rounded-full border-2 border-transparent focus:ring-2 focus:ring-blue-500 focus:outline-none focus:ring-opacity-50',
                wrapperCheckedDisabled: 'bg-blue-500',
                button: 'h-5 w-5 rounded-full bg-white shadow flex items-center justify-center text-gray-400 text-xs',
                buttonChecked: 'h-5 w-5 rounded-full bg-white shadow flex items-center justify-center text-blue-500 text-xs',
                checkedPlaceholder: 'rounded-full w-5 h-5 flex items-center justify-center text-gray-400 text-xs',
                uncheckedPlaceholder: 'rounded-full w-5 h-5 flex items-center justify-center text-gray-400 text-xs'
            },
            variants: {
                danger: {
                    wrapperChecked: 'bg-red-500 rounded-full',
                    wrapperCheckedDisabled: 'bg-red-500 rounded-full'
                },
                success: {
                    wrapperChecked: 'bg-green-500 rounded-full',
                    wrapperCheckedDisabled: 'bg-green-500 rounded-full'
                },
                box: {
                    wrapper: 'bg-gray-100 rounded-sm border-2 border-transparent focus:ring-2 focus:ring-blue-500 focus:outline-none focus:ring-opacity-50',
                    wrapperChecked: 'bg-blue-500 rounded-sm',
                    wrapperCheckedDisabled: 'bg-blue-500 rounded-sm',
                    button: 'h-6 w-6 rounded-sm bg-white shadow flex items-center justify-center text-gray-400 text-xs',
                    buttonChecked: 'h-6 w-6 rounded-sm  bg-white shadow flex items-center justify-center text-blue-500 text-xs',
                    checkedPlaceholder: 'rounded-sm w-6 h-6 flex items-center justify-center text-gray-400 text-xs',
                    uncheckedPlaceholder: 'rounded-sm w-6 h-6 flex items-center justify-center text-gray-400 text-xs'
                }
            }
        }
    },
    't-dropdown': {
        component: TDropdown,
        props: {
            fixedClasses: {
                button: 'flex justify-between items-center py-1 border-radius-xl px-4 bg-indigo-500 text-sm rounded text-gray-100 cursor-pointer hover:bg-indigo-600 hover:text-gray-100 focus:outline-none',
                wrapper: 'inline-flex flex-col',
                dropdownWrapper: 'relative z-10',
                dropdown: 'origin-top-left absolute left-0 w-56 rounded shadow mt-1',
                enterClass: 'opacity-0 scale-95',
                enterActiveClass: 'transition transform ease-out duration-100',
                enterToClass: 'opacity-100 scale-100',
                leaveClass: 'opacity-100 scale-100',
                leaveActiveClass: 'transition transform ease-in duration-75',
                leaveToClass: 'opacity-0 scale-95'
            },
            classes: {
                button: 'bg-blue-500 hover:bg-blue-600',
                dropdown: 'bg-white'
            },
            variants: {
                danger: {
                    button: 'bg-red-500 hover:bg-red-600',
                    dropdown: 'bg-red-50'
                }
            }
        }
    },
}
