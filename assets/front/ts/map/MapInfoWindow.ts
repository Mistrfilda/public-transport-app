export function getMainInfoWindowHtml(infoLinesHtml: string): string {
    return `
    <div class="border-t border-gray-200">
        <dl>
            ${infoLinesHtml}
        </dl>
    </div>    
    `;
}

export function getLightInfoWindowLine(text: string): string {
    return `
            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 border-b border-gray-200">
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    ${text}
                </dd>
            </div>    
    `;
}

export function getDarkInfoWindowLine(text: string): string {
    return `
            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 border-b border-gray-200">
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    ${text}
                </dd>
            </div>    
    `;
}